<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmiSchedule extends Model
{
  use HasFactory;

  protected $table = 'emi_schedule';

  protected $fillable = [
    'loan_id',
    'customer_id',
    'vendor_id',
    'emi_no',
    'emi_date',
    'amount',
    'late_fees',
    'status',
  ];

  public function customer()
  {
    return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
  }


  public function vendor()
  {
    return $this->belongsTo(Retailer::class, 'vendor_id');
  }

  public function retailer() 
  { 
    return $this->belongsTo(Retailer::class); 
  }


}
