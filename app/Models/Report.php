<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Meme;
use App\Models\User;

class Report extends Model
{
    protected $fillable = [
        'meme_id',
        'user_id',
        'reason',
        'details',
        'status',
    ];

    public function meme()
    {
        return $this->belongsTo(Meme::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
