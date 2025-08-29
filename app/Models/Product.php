<?php

namespace App\Models;
use App\Models\Brand;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $fillable = [
        'brand_id',
        'product_name',
        'product_price',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

}
