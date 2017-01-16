<?php namespace Bonsum\Http\Middleware;

use Closure;
use DB;

class LogDBQueries {


	protected $doLogQueries = false;


	public function __construct() {

		$this->doLogQueries = config('app.debug');
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
		if ($this->doLogQueries) {
			DB::connection()->enableQueryLog();
			DB::connection('mongodb')->enableQueryLog();
		}

		$response = $next($request);

		if ($this->doLogQueries) {
			$logs = [
				DB::connection()->getQueryLog(),
				DB::connection('mongodb')->getQueryLog()
			];
			foreach ($logs as $log) {
				if (!empty($log)) {
					logger($log);
				}
			}
		}

		return $response;
	}

}
