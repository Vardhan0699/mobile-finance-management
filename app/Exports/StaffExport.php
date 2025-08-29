<?php

namespace App\Exports;

use App\Models\Admin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StaffExport implements FromCollection, WithHeadings
{
  public function collection()
  {
    return Admin::select('id', 'firstname', 'lastname','mobile_no','role_id', 'email','zipcode', 'created_at')->get();
  }

  public function headings(): array
  {
    return ['ID', 'First Name', 'Last Name','Mobile','Role', 'Email','Zipcode', 'Created At'];
  }
}
