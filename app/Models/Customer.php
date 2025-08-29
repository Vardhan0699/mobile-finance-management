<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  protected $table = 'customer';

  protected $fillable = [
    'retailer_id',
    'customer_firstname',
    'customer_lastname',
    'date_of_birth',
    'father_name',
    'address1',
    'address2',
    'nearby',
    'city_id',
    'post',
    'mohalla',
    'village',
    'state_id',
    'pincode',
    'aadhaar_number',
    'mobile',
    'alternate_mobile',
    'selfie',
    'adharcard_front',
    'adharcard_back',
    'firebase_uid',
    'phone_verified_at',
  ];

  protected $hidden = [
    'password',
    'remember_token',
    'firebase_uid',
  ];

  protected $casts = [
    'email_verified_at' => 'timestamp',
    'phone_verified_at' => 'timestamp',
    'password' => 'hashed',
  ];

  public function isPhoneVerified()
  {
    return !is_null($this->phone_verified_at);
  }

  public function retailer()
  {
    return $this->belongsTo(Retailer::class, 'retailer_id');
  }

  public function brand()
  {
    return $this->belongsTo(Brand::class, 'brand_id');
  }

  public function product()
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  public function emiSchedules()
  {
    return $this->hasMany(EmiSchedule::class, 'customer_id');
  }
  public function loans()
  {
    return $this->hasMany(Loan::class, 'customer_id');
  }
  // Customer.php
  public function state()
  {
    return $this->belongsTo(States::class, 'state_id');
  }

  public function city()
  {
    return $this->belongsTo(Cities::class, 'city_id');
  }


}
