<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_only_active_posts()
    {
        Post::factory()->create([
            'is_draft' => true,
            'published_at' => null,
        ]);

        $publishedPost = Post::factory()->create([
            'is_draft' => false,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/posts');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $publishedPost->id,
                 ]);
    }

    /** @test */
    public function it_returns_404_for_draft_post()
    {
        $post = Post::factory()->create([
            'is_draft' => true,
        ]);

        $this->get("/posts/{$post->id}")
             ->assertStatus(404);
    }

    /** @test */
    public function only_author_can_update_post()
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'is_draft' => false,
            'published_at' => now(),
        ]);

        $this->actingAs($otherUser)
             ->put("/posts/{$post->id}", [
                 'title' => 'Updated',
                 'content' => 'Updated content',
             ])
             ->assertStatus(403);
    }

    /** @test */
    public function authenticated_user_can_create_post()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/posts', [
                 'title' => 'Test Post',
                 'content' => 'Post content',
             ])
             ->assertStatus(201);
    }
}
