<?php namespace Bonsum\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Build a response to an HTTP error suitable for production
	 * @param  \Illuminate\Http\Request       $request   				original HTTP request
	 * @param  HttpException 				  $exception 				HTTPexception that has been thrown
	 * @return  \Illuminate\Http\Response     Either an HTML page for the HTTP error or a concise encoding of the same
	 */
 	static public function responseForHTTPException($request, HttpException $exception) {

 		if ($request->ajax()) {
 				// error message and status code
                return response($exception->getMessage(), $exception->getStatusCode());
        } else {
		 		$data = [
                    "statusCode" => $exception->getStatusCode(),
                    "statusText" => SymfonyResponse::$statusTexts[$exception->getStatusCode()],
                    "errorMsg" => $exception->getMessage()
                 ];
                return response()->view('errors.error', $data, $exception->getStatusCode());
        }
 	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		if (!config('app.debug') || ($request->ajax() && !config('app.ajax_debug'))) {

			if (!$this->isHttpException($e)) {

				if ($e instanceof TokenMismatchException) {
					$e = new HttpException(SymfonyResponse::HTTP_UNAUTHORIZED, "TokenMismatch");

				} else if ($e instanceof ModelNotFoundException) {
					// thrown when findOrFail is called on a model
					$e = new NotFoundHttpException();

				} else {
					$e = new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
				}
			}
			return self::responseForHTTPException($request, $e);
		}
		return parent::render($request, $e);
	}

}
