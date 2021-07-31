<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParticipateInThreadsTest extends TestCase
{
    use DatabaseMigrations;

    function test_unauthenticated_user_may_not_add_replies()
    {
        $this->withExceptionHandling()
            ->post('/threads/someChannel/1/replies', [])
            ->assertRedirect('login');
    }


    function test_an_authenticated_user_may_participate_in_forum_thread()
    {
        $this->withExceptionHandling()->signIn();
        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make();
        $this->post($thread->path() . '/replies' ,  $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1,$thread->fresh()->replies_count);
    }

    function test_a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();
        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make(['body' => null]);

        $this->post($thread->path() . '/replies' ,  $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    function test_unauthorized_users_can_not_delete_replies ()
    {
        $this->withExceptionHandling();

        $reply = Reply::factory()->create();

        $this->delete("replies/{$reply->id}")
            ->assertRedirect('/login');         //Unauthenticated
        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    function test_authorized_users_can_delete_replies ()
    {
        $this->signIn();
        $reply = Reply::factory()->create( ['user_id' => auth()->id()] );
        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0,$reply->thread->fresh()->replies_count);
    }

    function test_authorized_users_can_update_replies ()
    {
        $this->signIn();
        $reply = Reply::factory()->create( ['user_id' => auth()->id()] );
        $this->patch("/replies/{$reply->id}",['body' => 'hello']);
        $this->assertDatabaseHas('replies', ['id'=> $reply->id,'body' => 'hello' ]);
    }

    function test_unauthorized_users_can_not_update_replies ()
    {
        $this->withExceptionHandling();

        $reply = Reply::factory()->create();

        $this->patch("replies/{$reply->id}")
            ->assertRedirect('/login');         //Unauthenticated
        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }
}
