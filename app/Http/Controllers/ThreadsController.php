<?php

namespace App\Http\Controllers;


use App\Channel;
use App\Rules\SpamFree;
use App\Thread;
use App\Filters\ThreadFilters;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ThreadsController extends Controller
{
	/**
	 * ThreadController constructor.
	 */
	public function __construct() {

		$this->middleware('auth')->except(['index', 'show']);
	}

	/**
     * Display a listing of the resource.
     *
	 * @param Channel $channel
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, Threadfilters $filters) {

	    $threads = $this->getThreads( $channel, $filters );

	    if(request()->wantsJson()) {
	    	return $threads;
	    }

	    return view('threads.index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

    	$this->validate($request, [
    		'title' => ['required', new SpamFree],
		    'body' => ['required', new SpamFree],
		    'channel_id' => 'required|exists:channels,id'
	    ]);

        $thread = Thread::create([
        	'user_id' => auth()->id(),
        	'channel_id' => $request['channel_id'],
        	'title' => $request['title'],
	        'body' => $request['body']
        ]);

        return redirect($thread->path())->with('flash', 'Your thread has been published!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread  $channelId
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread) {

    	if (auth()->check()) {
		    auth()->user()->read($thread);
	    }

        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread $channel
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread) {

    	//$thread->replies()->delete(); // can use this or whats in Thread model Boot function

	    $this->authorize('update', $thread);

    	$thread->delete();

    	if (request()->wantsJson()) {
    		return response([], 204);
	    }

    	return redirect('/threads');
    }

	/**
	 * @param Channel $channel
	 * @param ThreadFilters $filters
	 *
	 * @return mixed
	 */
	protected function getThreads( Channel $channel, Threadfilters $filters ) {
		$threads = Thread::latest()->filter( $filters );

		if ( $channel->exists ) {
			$threads->where( 'channel_id', $channel->id );
		}

		$threads = $threads->paginate(25);

		return $threads;
	}
}
