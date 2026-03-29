<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Meme;
use App\Models\WeeklyChallenge;
use App\Models\ChallengeEntry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $this->processSuccessfulPayment($session);
        }

        return response()->json(['status' => 'success']);
    }

    protected function processSuccessfulPayment($session)
    {
        $metadata = $session->metadata;
        $memeId = $metadata->meme_id ?? null;
        $challengeId = $metadata->challenge_id ?? null;

        if (!$memeId || !$challengeId) {
            Log::error('Stripe Webhook: Missing metadata', ['session_id' => $session->id]);
            return;
        }

        DB::transaction(function () use ($memeId, $challengeId, $session) {
            $meme = Meme::lockForUpdate()->find($memeId);
            $challenge = WeeklyChallenge::where('challenge_id', $challengeId)->first();

            if ($meme && $challenge) {
                // Update meme to contest status
                $meme->update([
                    'is_contest' => true,
                    'contest_week_id' => $challengeId,
                    'entry_paid_at' => now(),
                    'payment_provider' => 'stripe',
                    'payment_ref' => $session->id,
                ]);

                // Create challenge entry
                ChallengeEntry::updateOrCreate(
                    ['meme_id' => $meme->id, 'challenge_id' => $challengeId],
                    [
                        'user_id' => $meme->user_id,
                        'paid_amount_cents' => $session->amount_total,
                        'paid_at' => now(),
                        'payment_provider' => 'stripe',
                        'payment_ref' => $session->id,
                    ]
                );

                // Initial score calculation within the window
                $meme->recalculateScore();
            }
        });
    }
}
