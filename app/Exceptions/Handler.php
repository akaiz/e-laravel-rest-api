<?php

namespace App\Exceptions;

use Dotenv\Exception\ValidationException;
use Exception;
use HttpException;
use HttpResponseException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
  /**
   * A list of the exception types that are not reported.
   *
   * @var array
   */
  protected $dontReport = [
    AuthorizationException::class,
    HttpException::class,
    ModelNotFoundException::class,
    ValidationException::class,
  ];
  
  /**
   * A list of the inputs that are never flashed for validation exceptions.
   *
   * @var array
   */
  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];
  
  /**
   * Report or log an exception.
   *
   * @param Exception $exception
   *
   * @return void
   * @throws Exception
   */
  public function report(Exception $exception)
  {
    parent::report($exception);
  }
  
  /**
   * Render an exception into an HTTP response.
   *
   * @param Request $request
   * @param Exception $e
   *
   * @return Response
   */
  public function render($request, Exception $e)
  {
    if (env('APP_DEBUG')) {
      return parent::render($request, $e);
    }
    $status = Response::HTTP_INTERNAL_SERVER_ERROR;
    if ($e instanceof HttpResponseException) {
      $status = Response::HTTP_INTERNAL_SERVER_ERROR;
    } elseif ($e instanceof MethodNotAllowedHttpException) {
      $status = Response::HTTP_METHOD_NOT_ALLOWED;
      $e      = new MethodNotAllowedHttpException([], 'HTTP_METHOD_NOT_ALLOWED', $e);
    } elseif ($e instanceof NotFoundHttpException) {
      $status = Response::HTTP_NOT_FOUND;
      $e      = new NotFoundHttpException('HTTP_NOT_FOUND', $e);
    } elseif ($e instanceof AuthorizationException) {
      $status = Response::HTTP_FORBIDDEN;
      $e      = new AuthorizationException('HTTP_FORBIDDEN', $status);
    } elseif ($e instanceof ValidationException && $e->getResponse()) {
      $status = Response::HTTP_BAD_REQUEST;
      $e      = new ValidationException('HTTP_BAD_REQUEST', $status, $e);
    } elseif ($e instanceof UnauthorizedHttpException && $e->getMessage()) {
      $status = Response::HTTP_UNAUTHORIZED;
      $e      = new ValidationException('UNAUTHORIZED', $status, $e);
    } elseif ($e) {
      Log::error('[Internal Error]', ['error' => $e->getMessage()]);
      $e = new \Exception('HTTP_INTERNAL_SERVER_ERROR', $status, $e);
    }
    
    return response()->json([
      'success' => false,
      'status'  => $status,
      'message' => $e->getMessage()
    ], $status);
  }
}
