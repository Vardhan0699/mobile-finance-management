<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    protected $table = 'approve_pincode';
    protected $fillable = [
        'pincode',
    ];
}
