<?php

namespace Tests\Feature;

use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationsTest extends TestCase
{
	use RefreshDatabase;

	public function setUp() : void {

		parent:: setUp();

		$this->signIn();
	}

	/** @test */
	function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_not_by_current_user() {

		$thread = create('App\Thread')->subscribe();

		$this->assertCount(0, auth()->user()->notifications);

    	//then each time a new reply is left...
	    $thread->addReply([
	    	'user_id' => auth()->id(),
		    'body' => 'Some reply here'
	    ]);

	    // a notification should be prepared for the user
		$this->assertCount(0, auth()->user()->fresh()->notifications);


		//then each time a new reply is left...
		$thread->addReply([
			'user_id' => create('App\User')->id,
			'body' => 'Some reply here'
		]);

		$this->assertCount(1, auth()->user()->fresh()->notifications);
	}

	/** @test */
	function a_user_can_fetch_their_unread_notifications() {

		create(DatabaseNotification::class);

		$user = auth()->user();

		$this->assertCount(1, $this->getJson("/profiles/{$user->name}/notifications/")->json());
	}

	/** @test */
	function a_user_can_mark_a_notification_as_read() {

		create(DatabaseNotification::class);

		tap (auth()->user(), function ($user) {
			$this->assertCount(1, $user->unreadNotifications);

			$notificationID = $user->unreadNotifications->first()->id;

			$this->delete("/profiles/{$user->name}/notifications/{$notificationID}");

			$this->assertCount(0, $user->fresh()->unreadNotifications);
		});


	}
}
