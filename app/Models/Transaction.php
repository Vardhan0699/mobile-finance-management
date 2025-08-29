<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  use HasFactory;

  protected $table = 'transactions';

  protected $fillable = [
    'customer_id',
    'staff_id',
    'amount',
    'trans_id',
    'trans_date',
    'status',
    'mobile_no',
    'by',
    'paid_date',
  ];

  protected $casts = [
    'trans_date' => 'date',
  ];

  public function staff()
  {
    return $this->belongsTo(\App\Models\Admin::class, 'staff_id');
  }

  public function customer()
  {
    return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
  }



}
