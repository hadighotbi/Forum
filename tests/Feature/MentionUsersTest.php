<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
