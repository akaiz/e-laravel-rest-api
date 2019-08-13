<?php


namespace App\Http\Services;


use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService extends AccountService
{
  /**
   * @param $credentials
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function authenticate($credentials)
  {
    try {
      if ( ! $token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'invalid_credentials'], 401);
      }
    } catch (JWTException $e) {
      Log::error('[Login]', ['error' => $e->getMessage()]);
      return response()->json(['error' => 'failed_to_create_token'], 400);
    }
    
    return response()->json(compact('token'));
  }
  
  /**
   * @param $data
   *
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function register($data)
  {
    $validator = Validator::make($data, [
      'name'     => 'required|string|max:255',
      'email'    => 'required|string|email|max:255|unique:user',
      'income'   => 'required|numeric',
      'password' => 'required|min:8'
    ]);
    
    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }
    
    $customer           = new User();
    $customer->name     = $data['name'];
    $customer->email    = $data['email'];
    $customer->income   = $data['income'];
    $customer->password = Hash::make($data['password']);
    
    DB::beginTransaction();
    
    try {
      $customer->save();
      $this->createAccountCreditCard($customer->id);
    } catch (Exception $e) {
      DB::rollBack();
      throw $e;
    }
    
    DB::commit();
    
    $token = JWTAuth::fromUser($customer);
    
    return response()->json(compact('token'), 201);
  }
  
  public function logout($token)
  {
    try {
      JWTAuth::parseToken()->invalidate($token);
      
      return response()->json([
        'error'   => false,
        'message' => trans('auth.logged_out')
      ]);
    } catch (TokenExpiredException $exception) {
      Log::error('[Logout]', ['error' => $exception->getMessage()]);
      return response()->json([
        'error'   => true,
        'message' => trans('auth.token.expired')
      ], 401);
    } catch (TokenInvalidException $exception) {
      Log::error('[Logout]', ['error' => $exception->getMessage()]);
      return response()->json([
        'error'   => true,
        'message' => trans('auth.token.invalid')
      ], 401);
      
    } catch (JWTException $exception) {
      Log::error('[Logout]', ['error' => $exception->getMessage()]);
      return response()->json([
        'error'   => true,
        'message' => trans('auth.token.missing')
      ], 500);
    }
  }
}