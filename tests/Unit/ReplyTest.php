<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    function test_it_has_an_owner ()
    {
       $reply = Reply::factory()->create();

       $this->assertInstanceOf(User::class, $reply->owner);
    }

    function test_it_knows_if_it_was_just_published ()
    {
        $reply = Reply::factory()->create();

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }
}
