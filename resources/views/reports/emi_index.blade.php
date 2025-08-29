@extends('layouts.layout')

@section('content')

  <div class="page-content-wrapper">
    <div class="content-container">
    <div class="page-content">
      <div class="content-header">
      <h1 class="h4">EMI Report</h1>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Reports</li>
      </ul>
      </div>

      <div class="row">
      <div class="col-12">
        <div class="card table-card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
          <h5 class="flex-grow-1 mb-2 mb-md-0">All Customer EMI Reports</h5>

          <!-- Filter Button -->
          <div class="d-flex justify-content-between align-items-center mb-1">
          <button type="button" class="btn btn-primary" onclick="openReportFilterModal()">Filter</button>

          </div>
          <div class="btn-group">
          <a href="{{ route('admin.reports.emi.export.csv', request()->all()) }}"
            class="btn btn-light-primary btn-sm">Export CSV</a>
          </div>
        </div>

        <div class="card-body">
          <div class="table-responsive">
          <table class="table table-striped mb-0">
            <thead class="table-light">
            <tr>
              <th>S.no</th>
              <th>Customer Name</th>
              <th>Retailer Name</th>
              <th>Active Loan</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($emis as $emi)
          <tr>
            <td>{{ $loop->iteration + ($emis->currentPage() - 1) * $emis->perPage() }}</td>
            <!-- Customer Name -->
            <td>
            {{ ucfirst(strtolower($emi->customer_firstname ?? '-')) }}
            {{ ucfirst(strtolower($emi->customer_lastname ?? '')) }}
            </td>

            <!-- Retailer Name -->
            <td>
            {{ ucfirst(strtolower($emi->retailer->firstname ?? '-')) }}
            {{ ucfirst(strtolower($emi->retailer->lastname ?? '')) }}
            </td>

            <td>
            <strong>Total:</strong> {{ $emi->total_loan_count ?? 0 }}<br>

            @if(($emi->active_loan_count ?? 0) > 0)
          <strong>Active:</strong> {{ $emi->active_loan_count }}<br>
        @endif

            @if(($emi->inactive_loan_count ?? 0) > 0)
          <strong>Inactive:</strong> {{ $emi->inactive_loan_count }}<br>
        @endif

            @if(($emi->closed_loan_count ?? 0) > 0)
          <strong>Closed:</strong> {{ $emi->closed_loan_count }}<br>
        @endif
            </td>


            <td>
            <a href="{{ route('admin.show_emi_report', $emi->id)}}" class="btn btn-sm "
            title="View EMI Details">
            <i class="fas fa-eye"></i>
            </a>
            </td>
          </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center">No EMI records found.</td>
        </tr>
        @endforelse
            </tbody>
          </table>

          <div class="mt-3">
            {{ $emis->links('pagination::bootstrap-5') }}
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
    function openReportFilterModal() {
    Swal.fire({
      title: 'Filter Reports',
      width: '60%',
      html: `
  <form id="reportFilterForm" class="text-start">
  <div class="row g-3">
  <div class="col-md-6">
  <label for="swal_customer_name" class="form-label">Customer Name</label>
  <input type="text" id="swal_customer_name" class="form-control" placeholder="Customer Name" value="{{ request('customer_name') }}">
    </div>
  <div class="col-md-6">
  <label for="swal_retailer_name" class="form-label">Retailer Name</label>
  <input type="text" id="swal_retailer_name" class="form-control" placeholder="Retailer Name" value="{{ request('retailer_name') }}">
    </div>
  <div class="col-md-6">
  <label for="swal_loan_id" class="form-label">Loan ID</label>
  <input type="text" id="swal_loan_id" class="form-control" placeholder="Enter Loan ID" value="{{ request('loan_id') }}">
    </div>
  <div class="col-md-6">
  <label for="swal_date_from" class="form-label">Date From</label>
  <input type="date" id="swal_date_from" class="form-control" value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}">
    </div>
  <div class="col-md-6">
  <label for="swal_date_to" class="form-label">Date To</label>
  <input type="date" id="swal_date_to" class="form-control" value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}">
    </div>
    </div>
    </form>
  `,
      showCancelButton: true,
      showDenyButton: true,
      confirmButtonText: 'Apply Filter',
      denyButtonText: 'Reset',
      cancelButtonText: 'Close',
      customClass: {
      actions: 'justify-content-between'
      },
      preConfirm: () => {
      const params = new URLSearchParams();
      const customer = document.getElementById('swal_customer_name').value;
      const retailer = document.getElementById('swal_retailer_name').value;
      const loanId = document.getElementById('swal_loan_id').value;
      const dateFrom = document.getElementById('swal_date_from').value;
      const dateTo = document.getElementById('swal_date_to').value;

      if (customer) params.append('customer_name', customer);
      if (retailer) params.append('retailer_name', retailer);
      if (loanId) params.append('loan_id', loanId);
      if (dateFrom) params.append('date_from', dateFrom);
      if (dateTo) params.append('date_to', dateTo);

      window.location.href = `{{ route('admin.report.index') }}?` + params.toString();
      }
    }).then(result => {
      if (result.isDenied) {
      // Reset filter
      window.location.href = `{{ route('admin.report.index') }}`;
      }
    });
    }
  </script>


@endsection