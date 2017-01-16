<?php namespace Bonsum\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class LogOutDisabledUsers {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;


	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$user = $this->auth->user();

		if ($user && $user->disabled) {
			$this->auth->logout();
		}

		return $next($request);
	}

}
