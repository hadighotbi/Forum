<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LockThreads extends TestCase
{
    use DatabaseMigrations;

    function test_an_administrator_can_lock_any_thread()
    {
        $this->signIn();

        $thread = Thread::factory()->create();

        $thread->lock();

        $this->post($thread->path().'/replies', [
            'body' => 'foobar',
            'user_id' => User::factory()->create()->id
        ])->assertStatus(422);
    }
}
