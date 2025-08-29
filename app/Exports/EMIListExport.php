<?php

namespace App\Exports;

use App\Models\EmiSchedule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EMIListExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('emi_schedule')
            ->leftJoin('customer', 'customer.id', '=', 'emi_schedule.customer_id')
            ->leftJoin('retailer', 'retailer.id', '=', 'customer.retailer_id')
            ->leftJoin('transactions', 'transactions.customer_id', '=', 'customer.id')
            ->leftJoin('admin', 'admin.id', '=', 'transactions.staff_id')
            ->select(
                DB::raw("CONCAT(customer.customer_firstname, ' ', customer.customer_lastname) as customer_name"),
                'customer.father_name',
                'customer.mobile',
                'retailer.shop_name as retailer_shop_name',
                'customer.downpayment',
                'emi_schedule.status',
                DB::raw("CONCAT(admin.firstname, ' ', admin.lastname) as accepted_by"),
                'transactions.paid_date',
                DB::raw("CONCAT(customer.address1) as address")
            );

        if ($this->request->filled('customer_name')) {
            $query->where(function ($q) {
                $q->where('customer.customer_firstname', 'like', '%' . $this->request->customer_name . '%')
                  ->orWhere('customer.customer_lastname', 'like', '%' . $this->request->customer_name . '%');
            });
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('emi_schedule.emi_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('emi_schedule.emi_date', '<=', $this->request->date_to);
        }

        if ($this->request->filled('status')) {
            $query->where('emi_schedule.status', $this->request->status);
        }

        $records = $query->get();

        $data = [];
        $i = 1;

        foreach ($records as $record) {
            $data[] = [
                $i++,
                $record->customer_name ?? 'N/A',
                $record->father_name ?? 'N/A',
                $record->mobile ?? 'N/A',
                $record->retailer_shop_name ?? 'N/A',
                $record->downpayment_pending ?? 0,
                $record->status ?? 'N/A',
                $record->accepted_by ?? 'N/A',
                $record->paid_date ?? 'N/A',
                $record->address ?? 'N/A',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Customer Name',
            'Father Name',
            'Mobile No',
            'Retailer Shop Name',
            'Downpayment Pending Amount',
            'Status',
            'Accepted By',
            'Paid Date',
            'Address'
        ];
    }
}
