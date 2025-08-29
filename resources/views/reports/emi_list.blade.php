@extends('layouts.layout')

@section('content')

<div class="page-content-wrapper">
  <div class="content-container">
    <div class="page-content">
      <div class="content-header">
        <h1 class="h4">EMI List</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">EMI List</li>
        </ul>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card table-card"> 
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
              <h5 class="flex-grow-1 mb-2 mb-md-0">All Customer EMI List</h5>
              <!-- Filter Trigger Button -->
              <div class="d-flex justify-content-between align-items-center mb-1">
                <button type="button" class="btn btn-primary w-auto w-md-auto" onclick="openFilterModal()">
                  Filter
                </button>
              </div>
              <div class="btn-group">
                <a href="{{ route('admin.reports.emi_list.export.csv', request()->all()) }}" class="btn btn-light-primary btn-sm">Export CSV</a>
              </div> 
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center">S.no</th>
                      <th class="text-start">Customer Name</th>
                      <th class="text-center">Loan Id</th>
                      <th class="text-center">EMI Amount</th>
                      <th class="text-center">EMI Date</th>
                      <th class="text-center">Status</th>
                    </tr>
                  </thead>

                  <tbody>
                    @forelse ($emis as $index => $emi)
                    <tr>
                      <td class="text-center">{{ $index + $emis->firstItem() }}</td>
                      <td class="text-start">
                        {{ ucfirst(strtolower($emi->customer->customer_firstname ?? '-')) }} {{ ucfirst(strtolower($emi->customer->customer_lastname ?? '-')) }}
                      </td>

                      <td class="text-center">{{ $emi->loan_id }}</td>
                      <td class="text-center">
                        ₹{{ number_format($emi->amount, 2) }}{{ $emi->late_fees ? ' + ' . number_format($emi->late_fees, 2) : '' }}
                      </td>
                      <td class="text-center">{{ \Carbon\Carbon::parse($emi->emi_date)->format('d M Y') }}</td>
                      <td class="text-center">
                        @switch($emi->status)
                        @case('paid')
                        <span class="badge bg-success">Paid</span>
                        @break

                        @case('unpaid')
                        <span class="badge bg-warning text-dark">Unpaid</span>
                        @break

                        @case('recovery')
                        <span class="badge bg-danger">Recovery</span>
                        @break

                        @default
                        <span class="badge bg-secondary">Unknown</span>
                        @endswitch

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
  function openFilterModal() {
    Swal.fire({
      title: 'Filter EMI Records',
      html: `
<form id="filterForm" class="text-start">
<div class="mb-3">
<label for="customer_name" class="form-label">Customer Name</label>
<input type="text" id="swal_customer_name" class="form-control" placeholder="Customer Name" value="{{ request('customer_name') }}">
  </div>
<div class="mb-3">
<label for="status" class="form-label">Status</label>
<select id="swal_status" class="form-select">
<option value="">-- All --</option>
<option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
<option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
<option value="recovery" {{ request('status') == 'recovery' ? 'selected' : '' }}>Recovery</option>
  </select>
  </div>
<div class="mb-3">
<label for="date_from" class="form-label">Date From</label>
<input type="date" id="swal_date_from" class="form-control" value="{{ request('date_from') }}">
  </div>
<div class="mb-3">
<label for="date_to" class="form-label">Date To</label>
<input type="date" id="swal_date_to" class="form-control" value="{{ request('date_to') }}">
  </div>

  </form>
`,
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Apply Filter',
      denyButtonText: 'Reset Filter',
      cancelButtonText: 'Close',
      customClass: {
        actions: 'justify-content-between'
      },
      preConfirm: () => {
        const name = document.getElementById('swal_customer_name').value;
        const dateFrom = document.getElementById('swal_date_from').value;
        const dateTo = document.getElementById('swal_date_to').value;
        const status = document.getElementById('swal_status').value;

        const params = new URLSearchParams();
        if (name) params.append('customer_name', name);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        if (status !== '') params.append('status', status);

        window.location.href = `{{ route('admin.emi_list') }}?` + params.toString();
      }
    }).then((result) => {
      if (result.isDenied) {
        window.location.href = `{{ route('admin.emi_list') }}`;
      }
    });
  }
</script>




@endsection
