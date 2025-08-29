@extends('layouts.layout')

@section('content')
<div class="page-content-wrapper theme-1">
  <div class="content-container">
    <div class="page-content">

      <div class="content-header">
        <h1 class="h4">Recovery List</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Recovery</li>
        </ul>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card table-card"> 
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
              <h5 class="flex-grow-1 mb-2 mb-md-0">All Recovery EMI List</h5>
              <!-- Filter Trigger Button -->
              <div class="d-flex justify-content-between align-items-center mb-1">
                <button id="filterBtn" class="btn btn-primary w-auto w-md-auto" type="button">
                  Filter
                </button>
              </div>
            </div>

            <!-- Filter Button 
<div class="d-flex justify-content-end mb-2">
<button id="filterBtn" class="btn btn-outline-primary" type="button">
<i class="fas fa-filter me-1"></i> Filter
</button>
</div>-->

            <!-- Hidden Form to Submit Filters -->
            <form method="GET" id="filterForm" action="{{ route('admin.recovery.index') }}" class="d-none">
              <input type="hidden" name="status" id="filter_status" value="{{ request('status') }}">
              <input type="hidden" name="from_date" id="filter_from_date" value="{{ request('from_date') }}">
              <input type="hidden" name="to_date" id="filter_to_date" value="{{ request('to_date') }}">
            </form>

            <!-- Recovery Table -->
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center">#</th>
                      <th>Customer Name</th>
                      <th>Staff Name</th>
                      <th class="text-center">Mobile</th>
                      <th class="text-center">Amount</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">EMI Date</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($recoveryTransactions as $index => $emi)
                    @php
                    $customer = $emi->customer;
                    $emiDate = $emi->emi_date ? \Carbon\Carbon::parse($emi->emi_date) : null;
                    $isOverdue = $emiDate && $emiDate->lt(now());
                    @endphp
                    <tr>
                      <td class="text-center">{{ $index + 1 }}</td>
                      <td>
                        {{ ucfirst(strtolower($customer->customer_firstname ?? '-')) }} {{ ucfirst(strtolower($customer->customer_lastname ?? '-')) }}
                      </td>

                      <td>
                        {{ ucfirst(strtolower($emi->staff_firstname ?? '-')) }} {{ ucfirst(strtolower($emi->staff_lastname ?? '-')) }}
                      </td>


                      <td class="text-center">{{ $customer->mobile ?? '' }}</td>
                      <td class="text-center">₹{{ number_format($emi->amount, 2) }} + ₹{{ number_format($emi->late_fees) }} Late fees</td>
                      <td class="text-center">
                        @if ($emi->status === 'paid')
                        <span class="badge bg-success">Paid</span>
                        @elseif ($emi->status === 'recovery')
                        <span class="badge bg-danger">Recovery</span>
                        @else
                        <span class="badge bg-secondary">{{ ucfirst($emi->status) }}</span>
                        @endif
                      </td>
                      <td class="text-center {{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                        {{ $emiDate ? $emiDate->format('d M Y') : '-' }}
                      </td>
                      <td class="text-center">
                        <a href="{{ route('admin.recovery.view', $emi->id) }}" class="btn btn-sm" title="View EMI Details">
                          <i class="fas fa-eye"></i>
                        </a>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="8" class="text-center">No recovery EMIs found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                  {{ $recoveryTransactions->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
              </div>


            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filterBtn');
    const form = document.getElementById('filterForm');

    filterBtn.addEventListener('click', function () {
      Swal.fire({
        title: 'Filter Recovery',
        html:
        `<div class="text-start">
<label class="form-label mt-2">Status</label>
<select id="swal_status" class="form-select">
<option value="">All</option>
<option value="recovery" {{ request('status') == 'recovery' ? 'selected' : '' }}>Recovery</option>
<option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
  </select>

<label class="form-label mt-3">From Date</label>
<input type="date" id="swal_from_date" class="form-control" value="{{ request('from_date') }}">

<label class="form-label mt-3">To Date</label>
<input type="date" id="swal_to_date" class="form-control" value="{{ request('to_date') }}">
  </div>`,
        showCancelButton: true,
        confirmButtonText: 'Apply',
        cancelButtonText: 'Reset',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-secondary ms-2'
        },
        buttonsStyling: false,
        didOpen: () => {
          // Autofocus fix
          document.getElementById('swal_status').focus();
        }
      }).then((result) => {
        if (result.isConfirmed) {
          // Set values and submit
          document.getElementById('filter_status').value = document.getElementById('swal_status').value;
          document.getElementById('filter_from_date').value = document.getElementById('swal_from_date').value;
          document.getElementById('filter_to_date').value = document.getElementById('swal_to_date').value;
          form.submit();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          window.location.href = "{{ route('admin.recovery.index') }}";
        }
      });
    });
  });
</script>
@endsection
