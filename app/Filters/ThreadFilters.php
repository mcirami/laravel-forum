<?php


namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class ThreadFilters extends Filters
{

	protected $filters = ['by', 'popular', 'unanswered'];

	/**
	 * Filter query by given username
	 *
	 * @param $username
	 *
	 * @return mixed
	 */
	protected function by($username ) {

		// if request by someone we should filter by the given username
		$user = User::where( 'name', $username )->firstorFail();

		return $this->builder->where( 'user_id', $user->id );
	}

	/**
	 * Filter query by most popular threads
	 *
	 *
	 * @return $this
	 */
	protected function popular() {

		//clear 'latest()' query
		$this->builder->getQuery()->orders = [];
		return $this->builder->orderBy('replies_count', 'desc');
	}

	protected function unanswered() {

		return $this->builder->where('replies_count', 0);

	}
}