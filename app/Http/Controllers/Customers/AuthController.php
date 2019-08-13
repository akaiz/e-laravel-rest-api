<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
  protected $authService;
  
  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }
  
  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');
    
    return $this->authService->authenticate($credentials);
  }
  
  public function signup(Request $request)
  {
    $customer = $request->all();
    
    try {
      return $this->authService->register($customer);
    } catch (Exception $e) {
      Log::error('[Failed to register customer]', ['message' => $e->getMessage()]);
      
      return response()->json(['error' => 'failed_to_register_user'], 500);
    }
  }
  
  public function logout(Request $request) {
    $token = $request->header('Authorization');

    return $this->authService->logout($token);
  }
}
