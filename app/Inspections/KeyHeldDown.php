<?php


namespace App\Inspections;

use Exception;

class KeyHeldDown {

    /**
     * Handle a failed authorization attempt.
     *
     * @return bool
     *
     * @throws Exception
     */
	public function detect($body) {

		if(preg_match('/(.)\\1{4,}/', $body)) {
			throw new Exception('Your reply contains spam');
		}

		return false;
	}
}
