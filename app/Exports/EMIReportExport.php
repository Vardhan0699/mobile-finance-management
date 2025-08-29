<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class EMIReportExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Customer::with('retailer');

        if ($this->request->filled('customer_name')) {
            $query->where(function ($q) {
                $q->where('customer_firstname', 'like', '%' . $this->request->customer_name . '%')
                  ->orWhere('customer_lastname', 'like', '%' . $this->request->customer_name . '%');
            });
        }

        if ($this->request->filled('retailer_name')) {
            $query->whereHas('retailer', function ($q) {
                $q->where('firstname', 'like', '%' . $this->request->retailer_name . '%')
                  ->orWhere('lastname', 'like', '%' . $this->request->retailer_name . '%');
            });
        }

        if ($this->request->filled('loan_id')) {
            $query->where('loanID', 'like', '%' . $this->request->loan_id . '%');
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $this->request->date_to);
        }

        $customers = $query->get();

        $data = [];
        $i = 1;

        foreach ($customers as $customer) {
            $data[] = [
                $i++,
                $customer->customer_firstname . ' ' . $customer->customer_lastname,
                $customer->retailer ? $customer->retailer->firstname . ' ' . $customer->retailer->lastname : 'N/A',
                $customer->loanID,
                $customer->emi ?? 'N/A',
                $customer->disburse_amount ?? 'N/A',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['S.No', 'Customer Name', 'Retailer Name', 'Loan ID', 'EMI No', 'EMI Amount'];
    }
}
