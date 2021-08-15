<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;
    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();
        $this->thread = Thread::factory()->create();
    }

    function test_a_user_can_view_all_threads(){
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }
    function test_a_user_can_read_a_signle_thread(){
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    function test_a_user_can_filter_threads_according_to_a_channel(){
        $channel = Channel::factory()->create();
        $threadsInChannel = Thread::factory()->create(['channel_id' => $channel->id]);
        $threadsNotInChannel = Thread::factory()->create();

        $this->get('/threads/'. $channel->slug)
            ->assertSee($threadsInChannel->title)
            ->assertDontSee($threadsNotInChannel->title);
    }

    function test_a_user_can_filter_threads_by_any_username(){
        $this->signIn(  User::factory()->create(['name' => 'JohnDoe'])  );
        $threadByJohn = Thread::factory()->create(['user_id' => auth()->id()]);
        $threadNotByJohn = Thread::factory()->create();

        $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    function test_a_user_can_filter_threads_by_popularity(){
        $threadWithTwoReplies = Thread::factory()->create();
        Reply::factory()->count(2)->create(['thread_id' => $threadWithTwoReplies->id]);

        $threadWithThreeReplies = Thread::factory()->create();
        Reply::factory()->count(3)->create(['thread_id' => $threadWithThreeReplies->id]);

        $threadWithNoReply = $this->thread; //Thread with no reply

        $response = $this->getJson('/threads?popular=1')->json();
        $this->assertEquals([3,2,0] , array_column($response['data'], 'replies_count'));
    }

    function test_a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = Thread::factory()->create();
        Reply::factory()->count(2)->create(['thread_id' => $thread->id]);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertEquals(2,$response['total']);
    }

    function test_a_user_can_filter_threads_by_those_who_unanswered ()
    {
        $thread = Thread::factory()->create();
        Reply::factory()->create(['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();
        $this->assertCount(1,$response['data']);
    }
}
