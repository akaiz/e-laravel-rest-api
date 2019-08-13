<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Services\AccountService;

class AccountController extends Controller
{
  protected $accountService;
  
  public function __construct(AccountService $accountService)
  {
    $this->accountService = $accountService;
  }
  
  public function getProfile()
  {
    return $this->accountService->getAccount();
  }
}
