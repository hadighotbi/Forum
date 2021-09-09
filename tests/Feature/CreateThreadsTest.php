<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
  use DatabaseMigrations;
    function test_guests_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->get('threads/create')
            ->assertRedirect('login');

        $this->post(route('threads'))
            ->assertRedirect('login');
    }

    function test_a_user_can_create_new_forum_threads()  //create Thread
    {
        $this->signIn();
        $thread = Thread::factory()->make();   //Dont save to DB has no id
        $response = $this->post(route('threads'), $thread->toArray());    //Save

        $this->get($response->headers->get('Location'))     //Location of Redirect
        ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    function test_a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    function test_a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    function test_a_thread_requires_a_valid_channel()
    {
        Channel::factory()->count(2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    protected function publishThread ($overrides = [])  //posts a thread with signed in user
    {
        $this->withExceptionHandling()->signIn();
        $thread = Thread::factory()->make($overrides);
        return $this->post(route('threads'), $thread->toArray());  //return Response
    }

    function test_unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();
        $thread = Thread::factory()->create();

        $this->delete( $thread->path() )
            ->assertRedirect(route('login'));

        $this->signIn();

        $this->delete($thread->path())
            ->assertStatus(403);
    }

    function test_authorized_users_can_delete_threads()
    {
       $this->signIn();

       $thread = Thread::factory()->create(['user_id' => auth()->id()]);
       $reply  = Reply::factory()->create(['thread_id' => $thread->id]);

       $this->json('DELETE', $thread->path())
            ->assertStatus(204);

       $this->assertDatabaseMissing('threads' , ['id' => $thread->id]);
       $this->assertDatabaseMissing('replies' , ['id' => $reply->id]);
       $this->assertEquals(0,Activity::count());

    }

    function test_new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $user = User::factory()->unconfirmed()->create();

        $this->signIn($user);

        $thread = Thread::factory()->make();

        return $this->post('/threads', $thread->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash');
    }
}
