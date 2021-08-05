<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
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

        $this->post('/threads')
            ->assertRedirect('login');
    }

    function test_an_authenticated_user_can_create_new_forum_threads()  //create Thread
    {
        $this->signIn();
        $thread = Thread::factory()->make();   //Dont save to DB has no id
        $response = $this->post('/threads', $thread->toArray());    //Save

        $this->get($response->headers->get('Location'))     //Location of Redirect
        ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    function test_a_thread_requires_a_title()
    {
        $this->PublishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    function test_a_thread_requires_a_body()
    {
        $this->PublishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    function test_a_thread_requires_a_valid_channel()
    {
        Channel::factory()->count(2)->create();

        $this->PublishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->PublishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    public function PublishThread ($overrides = [])
    {
        $this->withExceptionHandling()->signIn();
        $thread = Thread::factory()->make($overrides);
        return $this->post('/threads', $thread->toArray());  //return Response
    }

    public function test_unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = Thread::factory()->create();
        $this->delete( $thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    public function test_authorized_users_can_delete_threads()
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




}
