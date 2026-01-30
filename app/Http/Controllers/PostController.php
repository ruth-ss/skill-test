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
        $posts = Post::active()
            ->with('user')
            ->paginate(20);

        return response()->json($posts);
    }

    /**
     * GET /posts/create
     * Only authenticated users
     */
    public function create()
    {
        return 'posts.create';
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
            'user_id'      => auth()->id(),
        ]);


        return response()->json($post, 201);
    }

    /**
     * GET /posts/{post}
     * Show a single active post
     */
    public function show(Post $post)
    {
        if (!$post->isActive()) {
            abort(404);
        }

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

        return 'posts.edit';
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

        $validated['is_draft'] = empty($validated['published_at']);
        $post->update($validated);


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
