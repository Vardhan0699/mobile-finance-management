<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DB::table('customer')
            ->join('retailer', 'customer.retailer_id', '=', 'retailer.id')
            ->join('cities', 'customer.city_id', '=', 'cities.id')
            ->join('states', 'customer.state_id', '=', 'states.id')
            ->join('loans', 'customer.id', '=', 'loans.customer_id')
            ->join('brand', 'loans.brand_id', '=', 'brand.id')
            ->join('product', 'loans.product_id', '=', 'product.id')
            ->select(
                'customer.id',
                // 'retailer.firstname as retailer_firstname',
                // 'retailer.lastname as retailer_lastname',
                // 'customer.customer_firstname',
                // 'customer.customer_lastname',
                DB::raw("CONCAT(retailer.firstname, ' ', retailer.lastname) as retailer_name"),
                DB::raw("CONCAT(customer.customer_firstname, ' ', customer.customer_lastname) as customer_name"),
                'customer.date_of_birth',
                'customer.father_name',
                'customer.address1',
                'customer.address2',
                'customer.nearby',
                'cities.name as city_name',
                'customer.village',
                'states.name as state_name',
                'customer.aadhaar_number',
                'customer.pincode',
                'loans.sell_price',
                'loans.disburse_amount',
                'brand.brand_name as brand_name',
                'product.product_name as product_name',
                'loans.imei1',
                'loans.imei2',
                'loans.downpayment',
                DB::raw('(loans.sell_price - loans.disburse_amount - loans.downpayment) as downpayment_pending'),
                'loans.emi',
                'loans.months',
                'customer.mobile',
                'customer.alternate_mobile',
                'customer.created_at'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Retailer Name',
            'Customer Name',
            'Date of Birth',
            'Father Name',
            'Address 1',
            'Address 2',
            'Nearby',
            'City',
            'Village',
            'State',
            'Aadhaar Number',
            'Pincode',
            'Sell Price',
            'Disburse Amount',
            'Brand',
            'Product',
            'IMEI 1',
            'IMEI 2',
            'Downpayment',
            'Downpayment Pending',
            'EMI',
            'Months',
            'Mobile No',
            'Alternate Mobile No',
            'Created At',
        ];
    }
}
