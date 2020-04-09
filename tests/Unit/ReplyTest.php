<?php

namespace Tests\Unit;

use App\User;
use App\Reply;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ReplyTest extends TestCase
{
	use RefreshDatabase, DatabaseMigrations;
	public $mockConsoleOutput = false;

    /** @test */
    function it_has_an_owner() {

    	$reply = create('App\Reply');

    	$this->assertInstanceOf('App\User', $reply->owner);
    }

	/** @test */
	function it_knows_if_it_was_just_published() {

		$reply = create('App\Reply');

		$this->assertTrue($reply->wasJustPublished());

		$reply->created_at = Carbon::now()->subMonth();

		$this->assertFalse($reply->wasJustPublished());

	}

    /** @test */
    function it_can_detect_all_mentioned_users_in_the_body() {

        $reply = create('App\Reply', [
            'body' => '@JaneDoe wants to talk to @JohnDoe'
        ]);

        $this->assertEquals(['JaneDoe', 'JohnDoe'], $reply->mentionedUsers());
    }

    /** @test */
    function it_wraps_mentioned_usernames_in_the_body_within_anchor_tags() {

        $reply = create('App\Reply', [
            'body' => 'Hello @JaneDoe.'
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/JaneDoe">@JaneDoe</a>.',
            $reply->body
        );

    }
}
