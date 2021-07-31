<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    function test_a_user_can_subscribe_to_threads()
    {
        $this->signIn();

        //Given we have a thread...
        $thread = Thread::factory()->create();

        //And the user subscribe to the thread...
        $this->post($thread->path() . '/subscriptions');

        $this->assertCount(1,$thread->fresh()->subscriptions);
//
    }

    function test_a_user_can_unsubscribe_from_threads ()
    {
        $this->signIn();
        $thread = Thread::factory()->create();

       $thread->subscribe();

        $this->delete($thread->path() . '/subscriptions');
        $this->assertCount(0,$thread->subscriptions);

    }

}
