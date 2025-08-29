@extends('layouts.layout')

@section('content')
<div class="page-content-wrapper theme-1">
  <div class="content-container">
    <div class="page-content">

      <div class="content-header">
        <h1 class="h4">EMI Details</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item">Recovery</li>
          <li class="breadcrumb-item active">View EMI</li>
        </ul>
      </div>

      <div class="container py-4">
        <div class="card shadow border-0">
          <div class="card-header bg-primary text-white">Customer Details</div>
          <div class="card-body">
            <p><strong>Loan ID:</strong> {{ $customer->loanID }}</p>
            <p><strong>Customer Name:</strong> {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}</p>
            <p><strong>Father's Name:</strong> {{ $customer->father_name }}</p>
            <p><strong>Mobile:</strong> {{ $customer->mobile }} | <strong>Alternate:</strong> {{ $customer->alternate_mobile }} </p>
            <p><strong>Address:</strong> {{ $customer->address1 }}, {{ $customer->mohalla }}, {{ $customer->post }}, {{ $customer->pincode }}</p>
            <p><strong>Aadhaar:</strong> {{ $customer->aadhaar_number }}</p>
            <p><strong>Sell Price:</strong> ₹{{ number_format($customer->sell_price) }}</p>
            <p><strong>Downpayment:</strong> ₹{{ number_format($customer->downpayment) }}</p>
            <p><strong>Disburse Amount:</strong> ₹{{ number_format($customer->disburse_amount) }}</p>

            <div class="card mt-4">
              <div class="card-header"><strong>Uploaded Documents</strong></div>
              <div class="card-body row">
                @foreach(['selfie' => 'Selfie', 'adharcard_front' => 'Aadhaar Front', 'adharcard_back' => 'Aadhaar Back'] as $field => $label)
                <div class="col-md-4 mb-3 text-center">
                  <p><strong>{{ $label }}</strong></p>
                  @if(!empty($imageUrls[$field]))
                  <img src="{{ $imageUrls[$field] }}" alt="{{ $label }}" class="img-fluid rounded border" style="max-height: 300px;">
                  @else
                  <p class="text-muted">Not Uploaded</p>
                  @endif
                </div>
                @endforeach
              </div>
            </div>

          </div>          
        </div>

        <div class="card shadow border-0">
          <div class="card-header bg-primary text-white">Retailer Details</div>
          <div class="card-body">
            <p><strong>Retailer Name:</strong> {{ $customer->retailer->firstname }} {{ $customer->retailer->lastname }}</p>
            <p><strong>Shop Name:</strong> {{ $customer->retailer->shop_name }}</p>
            <p><strong>Mobile:</strong> {{ $customer->retailer->mobile_no }}</p>
            <p><strong>Address:</strong> {{ $customer->retailer->address1 }}</p>
          </div>          
        </div>

        <div class="card shadow border-0 mt-4">
          <div class="card-header bg-dark text-white">EMI Schedule</div>
          <div class="card-body">
            @if($emiSchedules->isEmpty())
            <p>No EMI schedule available.</p>
            @else
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">EMI No</th>
                    <th class="text-center">EMI Date</th>
                    <th class="text-center">Amount (₹)</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($emiSchedules as $schedule)
                  <tr>
                    <td class="text-center">{{ $schedule->emi_no }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($schedule->emi_date)->format('d-m-Y') }}</td>
                    <td class="text-center">
                      ₹{{ number_format($schedule->amount + ($schedule->late_fees ?? 0)) }}
                      @if(!empty($schedule->late_fees) && $schedule->late_fees > 0)
                      <br>
                      <small>(₹{{ number_format($schedule->amount) }} + ₹{{ number_format($schedule->late_fees) }})</small>
                      @endif
                    </td>

                    <td class="text-center">
                      @switch($schedule->status)
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

        <div class="mt-3">
          <a href="{{ route('admin.recovery.index') }}" class="btn btn-secondary">Back</a>
        </div>

      </div>

    </div>
  </div>
</div>
@endsection
