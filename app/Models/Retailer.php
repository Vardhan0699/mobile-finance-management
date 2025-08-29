<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;

use Illuminate\Database\Eloquent\Model;

class Retailer extends Authenticatable
{
  	use Notifiable;
  
    protected $table = 'retailer';

    protected $fillable = [
        'firstname', 'lastname', 'shop_name', 'address1', 'address2',
        'state_id','city_id', 'zipcode', 'mobile_no', 'email', 'password'
    ];

    protected $hidden = ['password', 'remember_token'];
  
    public function sendPasswordResetNotification($token)
    {
      $this->notify(new \App\Notifications\RetailerResetPasswordNotification($token));
    }

    public function loan()
{
    return $this->belongsTo(Loan::class, 'loan_id');
}

public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id');
}


}
