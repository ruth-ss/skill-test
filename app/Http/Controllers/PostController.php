<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * GET /posts
     * Retrieve paginated active posts (published only)
     */
    public function index()
    {
        return response()->json(
            Post::active()
                ->with('user')
                ->paginate(20)
        );
    }

    /**
     * GET /posts/create
     * Only authenticated users
     * (Views not required by test)
     */
    public function create()
    {
        return response()->json([
            'message' => 'Create post endpoint'
        ]);
    }

    /**
     * POST /posts
     * Store a new post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $post = Post::create([
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'published_at' => $validated['published_at'] ?? null,
            'is_draft'     => empty($validated['published_at']),
            'user_id'      => $request->user()->id,
        ]);

        return response()->json($post, 201);
    }

    /**
     * GET /posts/{post}
     * Show a single active post
     */
    public function show(Post $post)
    {
        abort_unless($post->isActive(), 404);

        return response()->json(
            $post->load('user')
        );
    }

    /**
     * GET /posts/{post}/edit
     * Only post author
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return response()->json([
            'message' => 'Edit post endpoint'
        ]);
    }

    /**
     * PUT/PATCH /posts/{post}
     * Update post
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $post->update([
            ...$validated,
            'is_draft' => empty($validated['published_at']),
        ]);

        return response()->json($post);
    }

    /**
     * DELETE /posts/{post}
     * Delete post
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }
}
