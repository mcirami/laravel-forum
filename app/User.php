<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

	public function getRouteKeyName() {
		return 'name';
	}

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function threads() {
	    return $this->hasMany(Thread::class)->latest();
    }

    public function lastReply() {

    	return $this->hasOne(Reply::class)->latest();
    }

    public function activity() {
    	return $this->hasMany(Activity::class);
    }

    public function visitedThreadCacheKey($thread) {

	   return sprintf("users.%s.visits.%s", $this->id, $thread->id);

    }

    public function read($thread) {

	    cache()->forever($this->visitedThreadCacheKey($thread), Carbon::now());
    }

    public function getAvatarPathAttribute($avatar) {

        if( ! $avatar) {
            return asset('/storage/avatars/default.jpg');
        }

        return asset('/storage/' . $avatar);
    }
}
