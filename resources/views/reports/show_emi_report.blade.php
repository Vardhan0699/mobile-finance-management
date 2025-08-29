@extends('layouts.layout')

@section('content')

<div class="page-content-wrapper theme-1">
  <div class="content-container">
    <div class="page-content">

      <div class="content-header mb-4">
        <h1 class="h3"><i class="ti ti-file-text"></i> Full EMI Report</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Reports</li>
          <li class="breadcrumb-item active">Full EMI Report</li>
        </ul>
      </div>

      <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.report.index') }}" class="btn btn-outline-primary shadow-sm">
          <i class="ti ti-arrow-left"></i> Back
        </a>
      </div>

      {{-- Customer Info --}}
      <div class="card border-primary mb-4 shadow-sm">
        <div class="card-header bg-primary text-white fw-semibold">
          <i class="ti ti-user"></i> Customer Info
        </div>
        <div class="card-body">
          <p><strong>Name:</strong> {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}</p>
          <p><strong>Mobile:</strong> {{ $customer->mobile }}</p>
          <p><strong>Aadhaar:</strong> {{ $customer->aadhaar_number }}</p>
          <p><strong>Address:</strong> {{ $customer->address1 }}</p>
        </div>
      </div>

      {{-- Retailer Info --}}
      <div class="card border-info mb-4 shadow-sm">
        <div class="card-header bg-info text-white fw-semibold">
          <i class="ti ti-building-store"></i> Retailer Info
        </div>
        <div class="card-body">
          <p><strong>Retailer Name:</strong> {{ $customer->retailer->firstname ?? '-' }} {{ $customer->retailer->lastname ?? '-' }}</p>
          <p><strong>Shop Name:</strong> {{ $customer->retailer->shop_name ?? '-' }}</p>
          <p><strong>Mobile:</strong> {{ $customer->retailer->mobile_no ?? '-' }}</p>
          <p><strong>Email:</strong> {{ $customer->retailer->email ?? '-' }}</p>
        </div>
      </div>

      {{-- Loop Over Loans --}}
      @forelse($loans as $index => $loan)
      <div class="card border-success mb-3 shadow-sm">
          <div 
    class="card-header bg-success text-white fw-semibold toggle-header d-flex justify-content-between align-items-center" 
    data-target="loanDetails{{ $index }}" 
    style="cursor: pointer;"
>
    <div>
        <i class="ti ti-currency-rupee"></i> Loan & EMI Details - Loan ID: {{ $loan->loanID ?? $loan->id }}
    </div>
    
    {{-- Status Badge --}}
    <div>
        @switch($loan->status)
            @case(1)
                <span class="badge bg-primary">Active</span>
                @break
            @case(0)
                <span class="badge bg-warning text-dark">Inactive</span>
                @break
            @case(2)
                <span class="badge bg-danger">Closed</span>
                @break
            @default
                <span class="badge bg-secondary">Unknown</span>
        @endswitch
    </div>
</div>


          <div class="card-body loan-details" id="loanDetails{{ $index }}" style="display: none;">
              
              {{-- Product Info --}}
              <div class="mb-3">
                  <p><strong>Product:</strong> {{ $loan->product->product_name ?? '-' }} 
                      <small class="text-muted">(Brand: {{ $loan->brand->brand_name ?? '-' }})</small></p>
                  <p><strong>IMEI 1:</strong> {{ $loan->imei1 ?? '-' }}</p>
                  <p><strong>IMEI 2:</strong> {{ $loan->imei2 ?? '-' }}</p>
              </div>

              {{-- Loan Details --}}
              <div class="row">
                  <div class="col-md-4 mb-2"><strong>Device Price:</strong> ₹{{ number_format($loan->sell_price) }}</div>
                  <div class="col-md-4 mb-2"><strong>Loan Amount:</strong> ₹{{ number_format($loan->disburse_amount) }}</div>
                  <div class="col-md-4 mb-2"><strong>Total Interest:</strong> ₹{{ number_format($loan->total_interest) }}</div>
                  <div class="col-md-4 mb-2"><strong>Total Payment:</strong> ₹{{ number_format($loan->total_payment) }}</div>
                  <div class="col-md-4 mb-2"><strong>Monthly EMI:</strong> ₹{{ number_format($loan->emi) }}</div>
                  <div class="col-md-4 mb-2"><strong>EMI Duration:</strong> {{ $loan->months }} Months</div>
                  <div class="col-md-4 mb-2"><strong>Down Payment:</strong> ₹{{ number_format($loan->downpayment) }}</div>
              </div>

              {{-- EMI Schedule --}}
              <h6 class="mt-4 mb-2 fw-semibold">EMI Schedule</h6>
              @if($loan->emiSchedules->isEmpty())
                  <p class="text-muted">No EMI schedule available for this loan.</p>
              @else
                  <div class="table-responsive">
                      <table class="table table-bordered table-hover align-middle text-center">
                          <thead class="table-light">
                              <tr>
                                  <th>EMI No</th>
                                  <th>Date</th>
                                  <th>Amount (₹)</th>
                                  <th>Status</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach ($loan->emiSchedules as $emi)
                              <tr>
                                  <td>{{ $emi->emi_no }}</td>
                                  <td>{{ \Carbon\Carbon::parse($emi->emi_date)->format('d-m-Y') }}</td>
                                  <td>
                                      ₹{{ number_format($emi->amount) }}
                                      @if(!empty($emi->late_fees) && $emi->late_fees > 0)
                                      <span class="text-danger">+ ₹{{ number_format($emi->late_fees) }}</span>
                                      @endif
                                  </td>
                                  <td>
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
                              @endforeach
                          </tbody>
                      </table>
                  </div>
              @endif

          </div>
      </div>
      @empty
      <div class="alert alert-info">No loan records found for this customer.</div>
      @endforelse


    </div>
  </div>
</div>

{{-- Toggle Logic --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const headers = document.querySelectorAll('.toggle-header');
    headers.forEach(header => {
        header.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const allDetails = document.querySelectorAll('.loan-details');

            allDetails.forEach(detail => {
                if (detail.id === targetId) {
                    detail.style.display = (detail.style.display === 'none' || detail.style.display === '') ? 'block' : 'none';
                } else {
                    detail.style.display = 'none';
                }
            });
        });
    });
});
</script>

@endsection
