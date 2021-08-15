<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    function test_mentioned_user_in_a_reply_are_notified ()
    {
        //Given I have a user, Hadi, who is signed in.
        $hadi = User::factory()->create(['name' => 'hadi']);

        $this->signIn($hadi);

        //And another user, John Doe.
        $john = User::factory()->create(['name' => 'JohnDoe']);

        //If we have a thread.
        $thread = Thread::factory()->create();

        //And Hadi replies and mentions @JohnDoe.
        $reply = Reply::factory()->make([
            'body' => ' look at this @JohnDoe'
        ]);

        $this->json('post',$thread->path() . '/replies' ,  $reply->toArray());

        //And John Doe should be notified.
        $this->assertCount(1,$john->notifications);
    }

    function test_it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        User::factory()->create(['name' => 'johnDoe']);
        User::factory()->create(['name' => 'johnDoe2']);
        User::factory()->create(['name' => 'hadi']);

        $results = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(2, $results->json());
    }
}
