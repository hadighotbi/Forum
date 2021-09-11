<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BestReplyTest extends TestCase
{
   use DatabaseMigrations;

   function test_a_thread_creator_may_mark_any_reply_as_the_best_reply()
   {
        $this->signIn();

        $thread = Thread::factory()->create();

        $replies = Reply::factory()->create(['thread_id' => $thread->id]);

        $this->postJson(route('best-replies.store'));

        $this->assertTrue($replies[1]->isBest());
   }
}
