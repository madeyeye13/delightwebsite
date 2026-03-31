<?php

namespace Tests\Feature;

use App\Jobs\SendCommentAwaitingApprovalEmail;
use App\Livewire\Admin\Blog\CommentIndex;
use App\Livewire\Frontend\BlogComments;
use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class BlogCommentModerationTest extends TestCase
{
    use RefreshDatabase;

    // ── Frontend ─────────────────────────────────────────────────────────────

    public function test_submitting_a_comment_creates_pending_record_and_queues_email(): void
    {
        Queue::fake();

        $post = BlogPost::factory()->create();

        Livewire::test(BlogComments::class, ['postId' => $post->id])
            ->set('name', 'Chidi Okafor')
            ->set('email', 'chidi@example.com')
            ->set('body', 'Great fabric selection, love the store!')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('blog_comments', [
            'blog_post_id' => $post->id,
            'name' => 'Chidi Okafor',
            'email' => 'chidi@example.com',
            'is_approved' => false,
        ]);

        Queue::assertPushed(SendCommentAwaitingApprovalEmail::class);
    }

    public function test_comment_submission_fails_without_required_fields(): void
    {
        Queue::fake();

        $post = BlogPost::factory()->create();

        Livewire::test(BlogComments::class, ['postId' => $post->id])
            ->set('name', '')
            ->set('email', '')
            ->set('body', '')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'body']);

        Queue::assertNotPushed(SendCommentAwaitingApprovalEmail::class);
    }

    public function test_comment_submission_fails_with_invalid_email(): void
    {
        $post = BlogPost::factory()->create();

        Livewire::test(BlogComments::class, ['postId' => $post->id])
            ->set('name', 'Test User')
            ->set('email', 'not-an-email')
            ->set('body', 'A valid comment body here.')
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    public function test_unapproved_comments_do_not_appear_on_frontend(): void
    {
        $post = BlogPost::factory()->create();

        BlogComment::create([
            'blog_post_id' => $post->id,
            'name' => 'Pending Author',
            'email' => 'pending@example.com',
            'body' => 'This should not show.',
            'is_approved' => false,
        ]);

        $component = Livewire::test(BlogComments::class, ['postId' => $post->id]);

        $component->assertDontSee('This should not show.');
    }

    // ── Admin ─────────────────────────────────────────────────────────────────

    public function test_admin_can_approve_a_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = BlogPost::factory()->create();

        $comment = BlogComment::create([
            'blog_post_id' => $post->id,
            'name' => 'Pending User',
            'email' => 'user@example.com',
            'body' => 'Awaiting approval.',
            'is_approved' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(CommentIndex::class)
            ->call('approveComment', $comment->id);

        $this->assertDatabaseHas('blog_comments', [
            'id' => $comment->id,
            'is_approved' => true,
        ]);
    }

    public function test_admin_can_reject_an_approved_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = BlogPost::factory()->create();

        $comment = BlogComment::create([
            'blog_post_id' => $post->id,
            'name' => 'Approved User',
            'email' => 'user@example.com',
            'body' => 'Already approved.',
            'is_approved' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(CommentIndex::class)
            ->call('rejectComment', $comment->id);

        $this->assertDatabaseHas('blog_comments', [
            'id' => $comment->id,
            'is_approved' => false,
        ]);
    }

    public function test_admin_can_delete_a_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = BlogPost::factory()->create();

        $comment = BlogComment::create([
            'blog_post_id' => $post->id,
            'name' => 'Deletable User',
            'email' => 'del@example.com',
            'body' => 'This comment will be removed.',
            'is_approved' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(CommentIndex::class)
            ->call('deleteComment', $comment->id);

        $this->assertDatabaseMissing('blog_comments', ['id' => $comment->id]);
    }
}
