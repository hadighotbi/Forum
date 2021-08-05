<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Models\User;
use App\Notifications\YouWereMentioned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
       collect($event->reply->mentionedUsers())
           ->map( function ($name) {
              return User::where('name',$name)->first();
           })
           ->filter()                         //Gives the User Instances
           ->each( function ($user) use ($event) {
               $user->notify(new YouWereMentioned($event->reply));
           });

    }
}
