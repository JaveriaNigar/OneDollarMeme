<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemeComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'meme_id',
        'body',
        'parent_id',
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

    // Relation to Parent Comment (for replies)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Relation to Replies
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Check if this is a reply
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }
}
