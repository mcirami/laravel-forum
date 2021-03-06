<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function mentioned_users_in_a_reply_are_notified() {

        // Given I have a user, JohnDoe,  who is signed in.
        $john = create('App\User', ['name' => 'JohnDoe']);
        $this->signIn($john);

        // Add another user, JaneDoe
        $jane = create('App\User', ['name' => 'JaneDoe']);

        // If We have a thread
        $thread = create('App\Thread');

        // and JohnDoe Replies and mentions @janeDoe
        $reply = make('App\Reply', [
            'body' => '@JaneDoe look at this.'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        // Then, JaneDoe should be notified.
        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    function it_can_fetch_all_mentioned_users_starting_with_the_given_characters() {

        create('App\User', ['name' => 'johndoe']);
        create('App\User', ['name' => 'johndoe2']);
        create('App\User', ['name' => 'janedoe']);

        $results = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(2, $results->json());
    }
}
