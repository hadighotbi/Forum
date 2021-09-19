<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    function test_non_administrators_may_not_lock_the_threads()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread= Thread::factory()->create(['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store' , $thread))->assertStatus(403);

        $this->assertFalse(!! $thread->fresh()->locked);
    }

    function test_administrators_can_lock_threads()
    {
        $this->signIn(User::factory()->administrator()->create());

        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread));

        $this->assertTrue(!! $thread->fresh()->locked, 'Failed asserting that thread is locked');
    }

    function test_administrators_can_unlock_threads()
    {
        $this->signIn(User::factory()->administrator()->create());

        $thread = Thread::factory()->create(['user_id' => auth()->id(), 'locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that thread is unlocked');
    }

    function test_once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();
        $thread = Thread::factory()->create(['locked' => true]);

        $this->post($thread->path().'/replies', [
            'body' => 'foobar',
            'user_id' => auth()->id()
        ])
            ->assertStatus(422);
    }
}
