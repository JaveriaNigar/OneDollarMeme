<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'featured_image',
        'meta_description',
        'meta_keywords',
        'status',
        'published_at',
        'views_count',
    ];

    /**
     * Relations to eager load by default.
     */
    protected $with = ['appLinks'];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = static::generateUniqueSlug($blog->title);
            }
        });

        static::updating(function ($blog) {
            // Regenerate slug if title changes and slug wasn't manually set
            if ($blog->isDirty('title') && !$blog->isDirty('slug')) {
                $blog->slug = static::generateUniqueSlug($blog->title);
            }
        });
    }

    /**
     * Generate a unique slug from the title.
     */
    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the author of the blog.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the comments for the blog.
     */
    public function comments()
    {
        return $this->hasMany(BlogComment::class)->whereNull('parent_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get all comments (including replies).
     */
    public function allComments()
    {
        return $this->hasMany(BlogComment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get app links for the blog.
     */
    public function appLinks()
    {
        return $this->hasMany(BlogAppLink::class)->orderBy('sort_order');
    }

    /**
     * Scope for published blogs.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope for draft blogs.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for archived blogs.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Increment view count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Get excerpt from content.
     */
    public function getExcerptAttribute(): string
    {
        $content = strip_tags($this->content);
        return Str::limit($content, 200);
    }

    /**
     * Get excerpt with custom length.
     */
    public function excerpt(int $length = 200): string
    {
        $content = strip_tags($this->content);
        return Str::limit($content, $length);
    }

    /**
     * Get reading time in minutes.
     */
    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($words / 200));
    }
}
