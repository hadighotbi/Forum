<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Reply;
use App\Models\Thread;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ActivityTest extends TestCase
{
  use DatabaseMigrations;

  public function test_it_records_activity_when_a_thread_is_created()
  {
      $this->signIn();
      $thread = Thread::factory()->create();
      $this->assertDatabaseHas('activities',[
          'user_id' => auth()->id(),
          'subject_id' => $thread->id,
          'subject_type' => 'App\Models\Thread',
          'type' => 'created_thread'
      ]);
      $activity = Activity::first();

      $this->assertEquals($activity->subject->id, $thread->id);
  }

  public function test_it_records_activity_when_a_reply_is_created()
  {
      $this->signIn();
      $thread = Reply::factory()->create();

      $this->assertEquals(2,Activity::count());
  }

  function test_it_fetches_a_feed_for_any_user()
  {
     $this->signIn();
     $thread = Thread::factory()->count(2)->create(['user_id' => auth()->id()]);
     auth()->user()->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);
     $feed = Activity::feed(auth()->user(), 50);
     $this->assertTrue($feed->keys()->contains(
         Carbon::now()->format('Y-m-d')
     ));

      $this->assertTrue($feed->keys()->contains(
          Carbon::now()->subWeek()->format('Y-m-d')
     ));
  }

}
