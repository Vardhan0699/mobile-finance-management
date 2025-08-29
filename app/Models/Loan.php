<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'loans';

    protected $fillable = [
        'customer_id',
        'retailer_id',
        'loanID',
        'brand_id',
        'product_id',
        'imei1',
        'imei2',
        'sell_price',
        'disburse_amount',
        'downpayment',
        'emi',
        'months',
        'total_interest',
        'total_payment',
    ];

    // Relationship to Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function emiSchedules()
    {
        return $this->hasMany(EmiSchedule::class, 'loan_id','loanID');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Relationship to Retailer (Optional if needed)
    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

}
