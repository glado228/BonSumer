<?php namespace Bonsum\Http\Middleware;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Contracts\Events\Dispatcher;
use Bonsum\Events\TrapsTriggered;
use Closure;

class Traps {

	static public $traps = [
		'age',
		'telephone',
		'address',
		'date_of_birth'
	];


	/**
	 * Event dispatcher
	 * @var Illuminate\Contracts\Events\Dispatcher
	 */
	protected $events;


	public function __construct(Dispatcher $events) {
		$this->events = $events;
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
		if ($request->method() === 'POST') {
			foreach (self::$traps as $trap) {
				if ($request->has($trap)) {
					$this->events->fire(new TrapsTriggered($request));
					throw new BadRequestHttpException();
				}
			}
		}
		return $next($request);
	}

}
