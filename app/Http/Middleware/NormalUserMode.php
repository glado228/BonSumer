<?php namespace Bonsum\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App;

class NormalUserMode {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * If the special parameter "asNormalUser" is set, we downgrade the user (if any) to non-admin
	 * Useful if a logged-in admin wants to see the page as a normal user would see it.
	 *
	 * We perform the same downgrading in production for security reasons
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($request->get('asNormalUser') && $this->auth->check()) {
			$this->auth->user()->normal_user_mode = true;
		}

		return $next($request);
	}

}
