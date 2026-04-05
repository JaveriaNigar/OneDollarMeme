<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Meme;
use App\Models\Blog;
use App\Models\BlogComment;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'country',
        'profile_photo_path',
        'status',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=F9FAFB&color=7E3FF2';
    }

    public function memes()
    {
        return $this->hasMany(Meme::class);
    }

    public function challengePayouts()
    {
        return $this->hasMany(\App\Models\ChallengePayout::class, 'user_id');
    }

    public function brands()
    {
        return $this->hasMany(\App\Models\Brand::class);
    }

    public function sponsoredSubmissions()
    {
        return $this->hasMany(\App\Models\SponsoredSubmission::class);
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function blogComments()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function isBlogger(): bool
    {
        return $this->role === 'blogger' || $this->isAdmin();
    }

    public function isMemeUser(): bool
    {
        return $this->role === 'user' || $this->role === null || $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return in_array($this->email, [
            'javerianigar40@gmail.com',
            'official.onedollarmeme@gmail.com',
            'kinzasaeed688@gmail.com',
        ]);
    }

    public function canAccessAll(): bool
    {
        return $this->isAdmin();
    }
}
