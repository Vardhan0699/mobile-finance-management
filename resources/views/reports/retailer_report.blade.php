@extends('layouts.layout')

@section('content')

<div class="page-content-wrapper">
  <div class="content-container">
    <div class="page-content">
      <div class="content-header">
        <h1 class="h4">Retailer Report</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Report</li>
          <li class="breadcrumb-item active">Retailer Report</li>
        </ul>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card table-card"> 
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
              <h5 class="flex-grow-1 mb-2 mb-md-0">Retailer Report</h5>
              <!-- Filter Trigger Button -->
              <div class="d-flex justify-content-between align-items-center mb-1">
                <button type="button" class="btn btn-primary w-auto w-md-auto" onclick="openFilterModal()">
                  Filter
                </button>
              </div>
              <div class="btn-group">
                <a href="{{ route('admin.reports.retailer_report.export.csv', request()->all()) }}" class="btn btn-light-primary btn-sm">Export CSV</a>
              </div> 
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>S.no</th>
                      <th>Retailer Name</th>
                      <th>Shop Name</th>
                      <th>Total Landing (₹)</th>
                      <th>Total Downpayment (₹)</th>
                      <th>DP Pending</th>
                      <th>EMI Paid</th>
                      <th>DP Paid</th>
                      <th>Find Payment (₹)</th>
                      <th>New Phone</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($retailers as $key => $retailer)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ ucfirst($retailer->firstname) }} {{ ucfirst($retailer->lastname) }}</td>
                      <td>{{ $retailer->shop_name }}</td>
                      <td>₹ {{ number_format($retailer->total_landing, 2) ?? '0.00' }}</td>
                      <td>₹ {{ number_format($retailer->total_downpayment, 2) ?? '0.00' }}</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>₹ {{ number_format($retailer->find_payment, 2) ?? '0.00' }}</td>
                      <td>{{ $retailer->new_phone }}</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="10" class="text-center">No Retailer Data Found</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>

                <div class="mt-3">

                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function openFilterModal() {
    Swal.fire({
        title: 'Filter by Date',
        html:
            `<div class="mb-3 text-start">
                <label class="form-label">Start Date</label>
                <input type="date" id="start_date" class="form-control" value="{{ request()->start_date }}">
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">End Date</label>
                <input type="date" id="end_date" class="form-control" value="{{ request()->end_date }}">
            </div>`,
        showDenyButton: true,
        confirmButtonText: 'Apply Filter',
        denyButtonText: 'Clear Filter',
        showCancelButton: true,
        focusConfirm: false,
        preConfirm: () => {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (!startDate || !endDate) {
                Swal.showValidationMessage('Both Start Date and End Date are required');
                return false;
            }

            const url = new URL(window.location.href);
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);

            window.location.href = url.toString();
        }
    }).then((result) => {
        if (result.isDenied) {
            // Clear Filter: Remove query parameters
            const url = new URL(window.location.href);
            url.searchParams.delete('start_date');
            url.searchParams.delete('end_date');
            window.location.href = url.pathname;
        }
    });
}
</script>



@endsection
