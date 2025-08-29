<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;

class RetailerCustomerController extends Controller
{
  public function exportExcel()
  {
    $fileName = 'customers_' . now()->format('Ymd_His') . '.xlsx';
    return Excel::download(new CustomersExport, $fileName);
  }
}
