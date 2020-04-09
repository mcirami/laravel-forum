<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Notifications\YouWereMentioned;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event) {

        //inspect body of the reply for username mentions
       // $mentionedUsers = $event->reply->mentionedUsers();

        //then for each mentioned user, notify them.
        /*foreach($mentionedUsers as $name) {
            $user = User::whereName($name)->first();

            if($user) {
                $user->notify(new YouWereMentioned($event->reply));
            }
        }*/

        //refactored above

        User::whereIn('name', $event->reply->mentionedUsers())
            ->get()
            ->each(function ($user) use ($event) {
               $user->notify(new YouWereMentioned($event->reply));
            });
    }
}
