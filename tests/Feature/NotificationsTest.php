<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Database\Factories\DatabaseNotificationFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() :void
    {
        parent::setUp();

        $this->signIn();
    }

    function test_a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        $thread = Thread::factory()->create()
            ->subscribe();

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        //Each time a reply left...
        $thread->addReply([
            'user_id' => User::factory()->create()->id,
            'body' => 'some body here'
        ]);

        //A notification should prepared for the user.
        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    function test_a_user_can_fetch_their_unread_notifications()
    {
        DatabaseNotificationFactory::new()->create();

        $this->assertCount(1,
            $this->getJson("profiles/" . auth()->user()->name . "/notifications")->json()
        );
    }

    function test_a_user_can_mark_a_notification_as_read()
    {
        DatabaseNotificationFactory::new()->create();

        tap(auth()->user(), function ($user) {
            $this->assertCount(1, $user->unreadNotifications);

            $this->delete("profiles/{$user->name}/notifications/". $user->unreadNotifications->first()->id);

            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });
    }
}
