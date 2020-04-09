<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostForm;
use App\Thread;
use App\Reply;
use App\User;
use App\Rules\SpamFree;
use App\Notifications\YouWereMentioned;
use Illuminate\Support\Facades\Gate;

class RepliesController extends Controller
{
	public function __construct() {

		$this->middleware('auth');
	}

	public function index($channelId, Thread $thread) {

		return $thread->replies()->paginate(10);

	}

	public function store($channelId, Thread $thread, CreatePostForm $form) {

		/*if(Gate::denies('create', new Reply)) {
			return response(
				'You are posting too frequently. Please wait a minute', 422
			);
		}*/

		//try {

			//$this->authorize('create', new Reply);

			/*$this->validate(request(), [
				'body' => ['required', new SpamFree],
			]);*/

        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');

		/*} catch(\Exception $e) {

			return response('Sorry, your reply could not be saved at this time.', 422);
		}*/

		/*if (request()->expectsJson()) {
			return $reply->load('owner');
		}

		return back()->with('flash', 'Your reply has been left!');*/
    }

    public function destroy(Reply $reply) {

		$this->authorize('update', $reply);

		$reply->delete();

		if (request()->expectsJson()) {
			return response(['status' => 'Reply deleted']);
		}

		return back();
    }

    public function update(Reply $reply) {

	    $this->authorize('update', $reply);

        $this->validate(request(), [
            'body' => ['required', new SpamFree],
        ]);

        $reply->update(request(['body']));

    }
}
