<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
  public $table = "credit_card";
  /**
   * Get user(customer) credit card
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user() {
      return $this->belongsTo('App\Models\User');
    }
}
