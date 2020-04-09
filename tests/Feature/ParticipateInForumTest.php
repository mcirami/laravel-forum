<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\AuthenticationException;

class ParticipateInForumTest extends TestCase
{
	use RefreshDatabase;

	function unauthorized_users_may_not_add_replies() {
		$this->expectException('Illuminate\Auth\AuthenticationException');

		$this->post('/threads/some-channel/1/replies', [])->assertRedirect('/login');
	}

    /** @test */
    function an_authenticated_user_may_participate_in_forum_threads() {

    	// given we have a authenticated user

	    $this->signIn();

	    // and an existing thread
	    $thread = create('App\Thread');

	    // when the user adds a reply to the thread
	    $reply = make('App\Reply');
	    $this->post($thread->path() . '/replies' , $reply->toArray());

	    // then either reply should be in database
	    $this->assertDatabaseHas('replies', ['body' => $reply->body]);
	    $this->assertEquals(1, $thread->fresh()->replies_count);
    }

	/** @test */
	function a_reply_requires_a_body() {
		$this->withExceptionHandling()->signIn();

		// and an existing thread
		$thread = create('App\Thread');

		// when the user adds a reply to the thread
		$reply = make('App\Reply', ['body' => NULL]);

		$this->post($thread->path() . '/replies', $reply->toArray())->assertStatus(302);
	}

	/** @test */
	function unauthorized_users_cannot_delete_replies() {

		$this->withExceptionHandling();

		$reply = create('App\Reply');

		$this->delete("/replies/{$reply->id}")
			->assertRedirect('login');

		$this->signIn()
			->delete("/replies/{$reply->id}")
			->assertStatus(403);
	}

	/** @test */
	function authorized_users_can_delete_replies() {

		$this->signIn();

		$reply = create('App\Reply', ['user_id' => auth()->id()]);

		$this->delete("/replies/{$reply->id}")->assertStatus(302);

		$this->assertDatabaseMissing('replies', ['id' => $reply->id]);
		$this->assertEquals(0, $reply->thread->fresh()->replies_count);
	}

	/** @test */
	function authorized_users_can_update_replies() {

		$this->signIn();

		$reply = create('App\Reply', ['user_id' => auth()->id()]);

		$updateReply = 'You been changed, fool.';

		$this->patch("/replies/{$reply->id}", [ 'body' => $updateReply ]);

		$this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updateReply ]);
	}

	/** @test */
	function unauthorized_users_cannot_update_replies() {

		$this->withExceptionHandling();

		$reply = create('App\Reply');

		$this->patch("/replies/{$reply->id}")
		     ->assertRedirect('login');

		$this->signIn()
		     ->patch("/replies/{$reply->id}")
		     ->assertStatus(403);
	}

	/** @test */
	function replies_that_contain_spam_may_not_be_created() {

		$this->withExceptionHandling();

		$this->signIn();

		$thread = create('App\Thread');

		$reply = make('App\Reply', ['body' => 'Yahoo Customer Support']);

		$this->json('post', $thread->path() . '/replies', $reply->toArray())
		->assertStatus(422);
	}

	/** @test */
	function users_may_only_reply_a_maximum_of_once_per_minute() {

		$this->withExceptionHandling();

		$this->signIn();

		$thread = create('App\Thread');

		$reply = make('App\Reply', ['body' => 'My reply is here']);

		$this->post($thread->path() . '/replies', $reply->toArray())
		     ->assertStatus(201);

		$this->post($thread->path() . '/replies', $reply->toArray())
		     ->assertStatus(429);
	}

}
