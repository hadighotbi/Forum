<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    function test_a_user_has_a_profile()
    {
        $user = User::factory()->create();
        $this->get("/profiles/{$user->name}")
            ->assertSee($user->name);
    }

    function test_profiles_display_all_threads_create_by_the_associated_user()
    {
        $this->signIn();
        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->get("/profiles/" . auth()->user()->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }


}
