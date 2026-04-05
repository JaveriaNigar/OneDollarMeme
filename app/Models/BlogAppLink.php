<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogAppLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'label',
        'url',
        'icon',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the blog that owns the app link.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
