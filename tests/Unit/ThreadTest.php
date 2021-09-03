<?php

namespace Tests\Unit;

use App\Models\Thread;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();
        $this->thread = Thread::factory()->create();
    }

    function a_thread_can_make_a_string_path()
    {
        $this->assertEquals(
            '/threads/'. $this->thread->channel->slug.'/'.$this->thread->id, $this->thread->path());
    }

    function test_a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    function test_a_thread_has_a_creator()
    {
        $this->assertInstanceOf('App\Models\User', $this->thread->creator);
    }

    function test_a_thread_can_add_reply()
    {
        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies );
    }

    function a_thread_belongs_to_a_channel(){
        $this->assertInstanceOf('App\Models\Channel', $this->thread->channel);
    }

    function test_a_thread_can_be_subscribed_to ()
    {
        //Given we have a Thread
        $thread = Thread::factory()->create();

        //When the user subscribes to the thread
        $thread->subscribe($userId = 1);

        //Then we should be able to fetch all threads that the user has subscribed to.
        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    function test_a_thread_can_be_unsubscribed_from ()
    {
        $thread = Thread::factory()->create();

        $userId = 1;

        $thread->subscribe($userId);

        $thread->unsubscribe($userId);

        $this->assertEquals(
            0,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    function test_it_knows_if_the_authenticated_user_is_subscribed_to_it  ()
    {
        $thread = Thread::factory()->create();

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    function test_a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->signIn()
            ->thread
            ->subscribe()
            ->addReply([
                'body' => 'foobar',
                'user_id' => 1
            ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    function test_a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
       $this->signIn();
       $thread = Thread::factory()->create();

       tap(auth()->user() ,function ($user) use ($thread) {
           $this->assertTrue($thread->hasUpdatesFor($user));

           $user->read($thread);

           $this->assertFalse($thread->hasUpdatesFor($user));
       });
    }

    function test_a_thread_records_each_visit()
    {
        $thread = Thread::factory()->make(['id' => 1]);

        $thread->resetVisits();

        $this->assertSame(0, $thread->visits());

        $thread->recordVisit();

        $this->assertEquals(1, $thread->visits());

        $thread->recordVisit();

        $this->assertEquals(2, $thread->visits());
    }

}
