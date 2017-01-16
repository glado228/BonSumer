<?php namespace Bonsum\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Illuminate\Auth\Guard;


class Admin {

	/**
	 * authntication guard
	 * @var use \Illuminate\Auth\Guard
	 */
	protected $auth;


	public function __construct(Guard $auth) {
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
		if (!$this->auth->check()) {

			if ($request->ajax()) {

				return response('Unauthorized.', 401);

			} else {

				return redirect()->guest('auth/login');
			}

		} else if (!$this->auth->user()->admin || $this->auth->user()->disabled) {

			throw new AccessDeniedHttpException();
		}

		return $next($request);
	}

}
