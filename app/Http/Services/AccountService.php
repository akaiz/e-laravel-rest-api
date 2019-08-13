<?php


namespace App\Http\Services;

use App\Models\CreditCard;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use JWTAuth;

class AccountService
{
  /**
   * @return \Illuminate\Http\JsonResponse
   */
  public function getAccount()
  {
    try {
      if ( ! $user = JWTAuth::parseToken()->authenticate()) {
        return response()->json(['user_not_found'], 404);
      }
      
    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException  $e) {
      Log::warning('[Profile]', ['error' => $e->getMessage()]);
      return response()->json(['token_expired'], $e->getStatusCode());
      
    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
      Log::warning('[Profile]', ['error' => $e->getMessage()]);
      return response()->json(['token_invalid'], $e->getStatusCode());
      
    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
      Log::warning('[Profile]', ['error' => $e->getMessage()]);
      return response()->json(['token_absent'], $e->getStatusCode());
      
    }
    
    return response()->json(compact('user'));
  }
  
  /**
   * @param $id
   */
  protected function createAccountCreditCard($id)
  {
    $customerCreditCard       = new CreditCard();
    $customer                 = User::find($id);
    $customerCreditCard->type = $customer->income < 1000 ? 'SILVER' : 'GOLD';
    $customer->creditCards()->save($customerCreditCard);
  }
  
}