<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recovery extends Model
{
  protected $table = 'recovery';

  protected $fillable = [
    'staff_id',
    'loan_id',
    'emi_schedule_id',
  ];


}
