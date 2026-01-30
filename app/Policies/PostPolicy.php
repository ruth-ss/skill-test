<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Anyone can view list (handled by scopeActive)
     */
    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    /**
     * Anyone can view a post (controller checks isActive)
     */
    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Any authenticated user can create post
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only author can update
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Only author can delete
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
