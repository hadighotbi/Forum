<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    function test_a_user_can_fetch_their_most_recent_reply ()
    {
       $user = User::factory()->create();
       $reply = Reply::factory()->create(['user_id' => $user->id]);

       $this->assertEquals($reply->id,$user->lastReply->id);
    }

}
