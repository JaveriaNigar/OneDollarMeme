<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemeLike extends Model
{
    protected $fillable = [
        'user_id',
        'meme_id',
    ];

    // Relation to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relation to Meme
    public function meme(): BelongsTo
    {
        return $this->belongsTo(Meme::class);
    }
}
