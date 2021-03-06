<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use App\ThreadSubscription;
use App\Notifications\ThreadWasUpdated;
use function foo\func;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{

	use RecordsActivity;

	/**
	 * Don't auto-apply mass assignment protection.
	 *
	 * @var array
	 */
	protected $guarded = [];

	protected $with = ['creator', 'channel'];

	protected $appends = ['isSubscribedTo'];

	protected static function boot() {

		parent::boot();

		/*static::addGlobalScope('replyCount', function($builder){
			$builder->withCount('replies');
		});*/

		static::deleting(function ($thread) {
			$thread->replies->each->delete();

		});
	}

	/**
	 *
	 * Fetch a path to the current thread
	 * @return string
	 *
	 */
    public function path() {
    	return '/threads/' . $this->channel->slug . '/' . $this->id;
    }

	/**
	 * A thread belongs to a creator.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function creator() {
		return $this->belongsTo(User::class, 'user_id');
	}

	/**
	 * A thread belongs to a channel.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function channel() {
		return $this->belongsTo(Channel::class);
	}

	/**
	 * A thread may have many replies.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function replies() {
    	return $this->hasMany(Reply::class);
    }

	public function addReply($reply) {

		$reply = $this->replies()->create($reply);

		event(new ThreadReceivedNewReply($reply));

		return $reply;
	}

	public function scopeFilter($query, $filters) {
    	return $filters->apply($query);
	}

	public function subscribe($userId = null) {

    	$this->subscriptions()->create([
    		'user_id' => $userId ?: auth()->id()
	    ]);

    	return $this;
	}

	public function unsubscribe($userId = null) {
    	$this->subscriptions()->where('user_id', $userId ?: auth()->id())->delete();
	}

	public function subscriptions() {

    	return $this->hasMany(ThreadSubscription::class);

	}

	public function getIsSubscribedToAttribute() {

    	return $this->subscriptions()
		    ->where('user_id', auth()->id())
		    ->exists();
	}

	public function hasUpdatesFor($user) {


		$key = $user->visitedThreadCacheKey($this);

		//compare that carbon instance with the $thread->updated_at

		return $this->updated_at > cache($key);
	}
}
