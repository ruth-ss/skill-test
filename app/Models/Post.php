<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'is_draft',
        'published_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Active / Published posts
     */
    public function scopeActive(Builder $query)
    {
        return $query
            ->where('is_draft', false)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Check if post is published
     */
    public function isActive(): bool
    {
        return $this->is_draft === false
            && $this->published_at !== null
            && $this->published_at <= now();
    }
}
