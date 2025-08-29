@extends('retailerLogin.layout.layout')

@section('content')

<div class="page-content-wrapper">
  <div class="content-container">
    <div class="page-content">

      <div class="content-header mb-4">
        <h1 class="fw-bold text-primary">Customer Details</h1>
      </div>

      <div class="card shadow-lg p-4 mb-5 border-primary border-start border-4">
        <h4 class="mb-3 text-primary">Basic Information</h4>
        <div class="row g-3">
          <div class="col-md-4"><strong>Name:</strong> {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}</div>
          <div class="col-md-4"><strong>Father's Name:</strong> {{ $customer->father_name }}</div>
          <div class="col-md-4"><strong>Mobile:</strong> {{ $customer->mobile }}</div>
          <div class="col-md-4"><strong>DOB:</strong> {{ \Carbon\Carbon::parse($customer->date_of_birth)->format('d/m/Y') }}</div>
          <div class="col-md-4"><strong>Aadhaar Number:</strong> {{ $customer->aadhaar_number }}</div>
          <div class="col-md-4"><strong>State:</strong> {{ $customer->state->name ?? '-' }}</div>
          <div class="col-md-4"><strong>City:</strong> {{ $customer->city->name ?? '-' }}</div>
          <div class="col-md-4"><strong>Pincode:</strong> {{ $customer->pincode }}</div>
          <div class="col-md-4"><strong>Status:</strong> 
            @if($customer->status == 1)
            <span class="badge bg-success">Active</span>
            @else
            <span class="badge bg-danger">Inactive</span>
            @endif
          </div>
          <div class="col-md-4"><strong>Address:</strong> {{ $customer->address1 }}</div>
          <div class="col-md-4"><strong>Nearby:</strong> {{ $customer->nearby }}</div>
          <div class="col-md-4"><strong>Post:</strong> {{ $customer->post }}</div>
          <div class="col-md-4"><strong>Tola/Mohalla:</strong> {{ $customer->mohalla }}</div>
        </div>

        <h5 class="mt-4 mb-3 text-primary">Uploaded Images</h5>
        <div class="row g-3">
          @foreach (['selfie' => 'Selfie', 'adharcard_front' => 'Aadhaar Front', 'adharcard_back' => 'Aadhaar Back'] as $field => $label)
          <div class="col-12 col-sm-4">
            <div class="border rounded p-3 h-100 text-center shadow-sm">
              <p class="fw-semibold mb-2">{{ $label }}</p>
              @if(!empty($imageUrls[$field]))
              <img src="{{ $imageUrls[$field] }}" class="img-fluid rounded mb-2" style="max-height: 150px;">
              @else
              <p class="text-muted">No Image</p>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <h4 class="fw-bold mb-3 text-primary">Loan Details</h4>

      <div class="accordion" id="loanAccordion">
        @forelse($loans as $index => $loan)
        <div class="accordion-item mb-3 shadow-sm border-start border-3 border-info">
          <h2 class="accordion-header" id="heading{{ $index }}">
            <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
              Loan ID: {{ $loan->loanID }} | Brand: {{ optional($loan->brand)->brand_name ?? 'N/A' }} | Product: {{ optional($loan->product)->product_name ?? 'N/A' }}
            </button>
          </h2>
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

          <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
               aria-labelledby="heading{{ $index }}" data-bs-parent="#loanAccordion">
            <div class="accordion-body">

              <div class="row g-3 mb-3">
                <div class="col-md-3"><strong>Sell Price:</strong> ₹{{ number_format($loan->sell_price, 2) }}</div>
                <div class="col-md-3"><strong>Disbursement:</strong> ₹{{ number_format($loan->disburse_amount, 2) }}</div>
                <div class="col-md-3"><strong>Status:</strong> 
                  @if($loan->status == 1)
                  <span class="badge bg-success">Active</span>
                  @elseif($loan->status == 2)
                  <span class="badge bg-secondary">Closed</span>
                  @else
                  <span class="badge bg-danger">Inactive</span>
                  @endif
                </div>
              </div>

              <h5 class="mt-4 mb-2 text-info">EMI Schedule</h5>
              @if($loan->emiSchedules->count() > 0)
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                  <thead class="table-info">
                    <tr>
                      <th>EMI No</th>
                      <th>Date</th>
                      <th>Amount</th>
                      <th>Late Fees</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($loan->emiSchedules as $emi)
                    <tr>
                      <td>{{ $emi->emi_no }}</td>
                      <td>{{ \Carbon\Carbon::parse($emi->emi_date)->format('d-m-Y') }}</td>
                      <td>₹{{ number_format($emi->amount, 2) }}</td>
                      <td>₹{{ number_format($emi->late_fees ?? 0, 2) }}</td>
                      <td>
                        @switch($emi->status)
                        @case('paid') <span class="badge bg-success">Paid</span> @break
                        @case('unpaid') <span class="badge bg-warning text-dark">Unpaid</span> @break
                        @case('recovery') <span class="badge bg-danger">Recovery</span> @break
                        @default <span class="badge bg-secondary">Unknown</span>
                        @endswitch
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <p class="text-muted">No EMI Schedule available.</p>
              @endif

            </div>
          </div>
        </div>
        @empty
        <p class="text-muted">No Loans found for this customer.</p>
        @endforelse
      </div>

    </div>
  </div>
</div>

<!-- ✅ Bootstrap 5 JS only (avoid mixing versions) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional: jQuery and Select2 if used elsewhere -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endsection
