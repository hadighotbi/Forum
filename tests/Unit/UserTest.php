<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    function test_a_user_can_fetch_their_most_recent_reply()
    {
        $user = User::factory()->create();

        $reply = Reply::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    function test_a_user_can_determine_their_avatar_path()
    {
        $user = User::factory()->create();

        $this->assertEquals(asset('storage/avatars/default.png'), $user->avatar_path);

        $user->avatar_path = 'storage/avatars/me.jpg';

        $this->assertEquals(asset('storage/avatars/me.jpg'), $user->avatar_path);
    }

}
