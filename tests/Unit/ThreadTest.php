<?php

namespace Tests\Unit;

//use Illuminate\Notifications\Notification;
use App\Notifications\ThreadWasUpdated;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use \Illuminate\Support\Facades\Notification;

class ThreadTest extends TestCase
{
	use RefreshDatabase;
	protected $thread;

	public function setUp(): void {
		parent::setUp();
		$this->thread = factory('App\Thread')->create();
	}

	/** @test */
	function a_thread_can_make_a_string_path() {
		$thread = create('App\Thread');

		$this->assertEquals('/threads/' . $thread->channel->slug . '/' . $thread->id, $thread->path());
	}

    /** @test */
    function a_thread_has_replies() {

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

	/** @test */
	function a_thread_has_a_creator() {

		$this->assertInstanceOf('App\User', $this->thread->creator);
	}

	/** @test */
	function a_thread_can_add_a_reply() {

		$this->thread->addReply([
			'body' => 'Foobar',
			'user_id' => 1
		]);

		$this->assertCount(1, $this->thread->replies);

	}

	/** @test */
	function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added() {

		Notification::fake();

		$this->signIn()
			->thread
			->subscribe()
			->addReply([
				'body' => 'foobar',
				'user_id' => 999
			]);

		Notification::assertSentTo(auth()->user(),ThreadWasUpdated::class);
	}

	/** @test */
	function a_thread_belongs_to_a_channel() {

		$thread = create('App\Thread');

		$this->assertInstanceOf('App\Channel', $thread->channel);
	}

	/** @test */
	function a_thread_can_be_subscribed_to() {

		//Given we have a thread
		$thread = create('App\Thread');

		//When the user subscribes to the thread
		$thread->subscribe($userId = 1);

		// Then we shoudl be able to fetch all threads that the user has subscribed to.
		$this->assertEquals(1, $thread->subscriptions()->where('user_id', $userId)->count());

	}

	/** @test */
	function a_thread_can_be_unsubscribed_from() {

		//Given we have a thread
		$thread = create('App\Thread');

		// and a user who is subscribed to the thread
		$thread->subscribe($userId = 1);

		$thread->unsubscribe($userId);

		$this->assertCount(0, $thread->subscriptions);
	}

	/** @test */
	function it_knows_if_the_authenticated_user_is_subscribed_to_it() {

		//Given we have a thread
		$thread = create('App\Thread');

		$this->signIn();

		$this->assertFalse($thread->isSubscribedTo);

		$thread->subscribe();

		$this->assertTrue($thread->isSubscribedTo);
	}

	/** @test */
	function a_thread_can_check_if_the_authenticated_user_has_read_all_replies() {

		$this->signIn();

		$thread = create('App\Thread');


		tap(auth()->user(), function ($user) use ($thread) {

			$this->assertTrue($thread->hasUpdatesFor($user));

			// simulate user visited thread
			$user->read($thread);

			$this->assertFalse($thread->hasUpdatesFor($user));

		});

	}
}
