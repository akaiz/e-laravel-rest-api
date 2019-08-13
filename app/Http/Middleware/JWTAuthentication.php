<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JWTAuthentication extends BaseMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Closure $next
   *
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    try {
      $customer = JWTAuth::parseToken()->authenticate();
    } catch (Exception $e) {
      if ($e instanceof TokenInvalidException) {
        Log::error('[JWT Middleware]', ['error' => $e->getMessage()]);
        return response()->json(['status' => 'Token is invalid.']);
      } elseif ($e instanceof TokenExpiredException) {
        Log::error('[JWT Middleware]', ['error' => $e->getMessage()]);
        return response()->json(['status' => 'Token is expired.']);
      } else  {
        return response()->json(['status' => 'Authorization token not found.']);
      }
    }
    
    return $next($request);
  }
}
