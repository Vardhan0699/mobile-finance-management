<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Transaction::with('staff')
            ->select('id', 'customer_id', 'staff_id', 'amount', 'trans_id', 'trans_date', 'status', 'mobile_no', 'by', 'paid_date')
            ->get()
            ->map(function ($transaction) {
                return [
                    'ID'            => $transaction->id,
                    'Customer ID'   => $transaction->customer_id,
                    'Staff Name'    => $transaction->staff ? $transaction->staff->firstname . ' ' . $transaction->staff->lastname : 'N/A',
                    'Amount'        => $transaction->amount,
                    'Transaction ID'=> $transaction->trans_id,
                    'Transaction Date'=> $transaction->trans_date,
                    'Status'        => $transaction->status,
                    'Mobile No'     => $transaction->mobile_no,
                    'By'            => $transaction->by,
                    'Paid Date'     => $transaction->paid_date,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer ID',
            'Staff Name',
            'Amount',
            'Transaction ID',
            'Transaction Date',
            'Status',
            'Mobile No',
            'By',
            'Paid Date',
        ];
    }
}
