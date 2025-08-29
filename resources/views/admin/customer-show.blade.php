@extends('layouts.layout')

@section('content')

  <body class="theme-1">
    <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
      <div class="content-header">
        <h1>View Customer</h1>
        <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Customer</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.customer_list') }}">Customer List</a></li>
        <li class="breadcrumb-item">View Customer</li>
        </ul>
      </div>

      <div class="row">
        <div class="col-12">
        <div class="row">
          <div class="col-12">
          <div id="step-container">
            <!-- Step 1: Customer Info -->
            <div class="card step" id="step-1">
            <div class="card-header">
              <h4>Customer Information</h4>
            </div>
            <div class="card-body row">
              <div class="col-md-6 mb-3">
              <label>First Name</label>
              <input type="text" name="customer_firstname" class="form-control"
                value="{{ old('customer_firstname', $customers->customer_firstname) }}" disabled>
              </div>
              <div class="col-md-6 mb-3">
              <label>Last Name</label>
              <input type="text" name="customer_lastname" class="form-control"
                value="{{ old('customer_lastname', $customers['customer_lastname'] ?? '') }}" disabled>
              </div>
              <div class="col-md-6 mb-3">
              <label>Date of Birth</label>
              <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                value="{{ old('date_of_birth', $customers['date_of_birth'] ?? '') }}" disabled>
              </div>

              <div class="col-md-6 mb-3">
              <label>Father's Name</label>
              <input type="text" name="father_name" class="form-control"
                value="{{ old('father_name', $customers['father_name'] ?? '') }}" disabled>
              </div>
              <div class="col-md-6 mb-3">
              <label>Address 1</label>
              <input type="text" name="address1" class="form-control"
                value="{{ old('address1', $customers['address1'] ?? '') }}" disabled>
              </div>
              <div class="col-md-6 mb-3">
              <label>Address 2</label>
              <input type="text" name="address2" class="form-control"
                value="{{ old('address2', $customers['address2'] ?? '') }}" disabled>
              </div>
              <div class="col-md-4 mb-3">
              <label>State</label>
              <select name="state_id" id="state_id" class="form-select" disabled>
                <option value="">Select State</option>
                @foreach ($states as $state)
          <option value="{{ $state->id }}" {{ old('state_id', $customers['state_id'] ?? '') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
          @endforeach
              </select>
              </div>
              <div class="col-md-4 mb-3">
              <label>City</label>
              <select name="city_id" id="city_id" class="form-select" disabled>
                <option value="">Select City</option>
                @foreach ($cities as $city)
          <option value="{{ $state->id }}" {{ old('city_id', $customers['city_id'] ?? '') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
          @endforeach
              </select>
              </div>
              <div class="col-md-4 mb-3">
              <label>Post</label>
              <input type="text" name="post" class="form-control"
                value="{{ old('post', $customers['post'] ?? '') }}" disabled>
              </div>
              <div class="col-md-4 mb-3">
              <label>Village</label>
              <input type="text" name="village" class="form-control"
                value="{{ old('village', $customers['village'] ?? '') }}" disabled>
              </div>
              <div class="col-md-4 mb-3">
              <label>Nearby</label>
              <input type="text" name="nearby" class="form-control"
                value="{{ old('nearby', $customers['nearby'] ?? '') }}" disabled>
              </div>
              <div class="col-md-4 mb-3">
              <label>Tola/Mohalla</label>
              <input type="text" name="mohalla" class="form-control"
                value="{{ old('mohalla', $customers['mohalla'] ?? '') }}" disabled>
              </div>
              <div class="col-md-4 mb-3">
              <label>Pincode</label>
              <input type="text" name="pincode" id="pincode" class="form-control"
                value="{{ old('pincode', $customers['pincode'] ?? '') }}" disabled>
              </div>
              <div class="col-md-6 mb-3">
              <label>Aadhar Number</label>
              <input type="text" name="aadhaar_number" class="form-control"
                value="{{ old('aadhaar_number', $customers['aadhaar_number'] ?? '') }}" disabled>
              </div>
              <div class="col-md-6 mb-3">
              <label>Mobile</label>
              <input type="text" name="mobile" class="form-control"
                value="{{ old('mobile', $customers['mobile'] ?? '') }}" disabled>
              </div>
              <div class="col-md-6 mb-3">
              <label>Alternate Mobile</label>
              <input type="text" name="alternate_mobile" class="form-control"
                value="{{ old('alternate_mobile', $customers['alternate_mobile'] ?? '') }}" disabled>
              </div>
              <div class="text-end"><button type="button" class="btn btn-primary next">Next</button></div>
            </div>
            </div>

            <div class="card step" id="step-2">
    <div class="card-header">
        <h4>Loan & EMI Details</h4>
    </div>
    <div class="card-body">

        @if($loans->isEmpty())
            <div class="alert alert-info">No Loan records found for this customer.</div>
        @else

            <div class="accordion" id="loanAccordion">

                @foreach($loans as $index => $loan)
                    <div class="accordion-item mb-3 border border-primary rounded">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button collapsed fw-bold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                Loan ID: {{ $loan->loanID ?? $loan->id }}
                            </button>
                        </h2>

                        <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#loanAccordion">
                            <div class="accordion-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Brand</label>
                                        <select class="form-select" disabled>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ $loan->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Product</label>
                                        <select class="form-select" disabled>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ $loan->product_id == $product->id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>IMEI 1</label>
                                        <input type="text" class="form-control" value="{{ $loan->imei1 }}" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>IMEI 2</label>
                                        <input type="text" class="form-control" value="{{ $loan->imei2 }}" disabled>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Device Price (₹)</label>
                                        <input type="text" class="form-control" value="{{ number_format($loan->sell_price) }}" disabled>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>EMI Duration</label>
                                        <input type="text" class="form-control" value="{{ $loan->months }}" disabled>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Down Payment (₹)</label>
                                        <input type="text" class="form-control" value="{{ number_format($loan->downpayment) }}" disabled>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Loan Amount (₹)</label>
                                        <input type="text" class="form-control" value="{{ number_format($loan->disburse_amount) }}" disabled>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Total Interest (₹)</label>
                                        <input type="text" class="form-control" value="{{ number_format($loan->total_interest) }}" disabled>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Total Payment (₹)</label>
                                        <input type="text" class="form-control" value="{{ number_format($loan->total_payment) }}" disabled>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Monthly EMI (₹)</label>
                                        <input type="text" class="form-control" value="{{ number_format($loan->emi) }}" disabled>
                                    </div>
                                </div>

                                <h6 class="mt-3 mb-2">EMI Schedule</h6>

                                @if($loan->emiSchedules->isEmpty())
                                    <p>No EMI schedule available for this loan.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>EMI No</th>
                                                    <th>EMI Date</th>
                                                    <th>Amount (₹)</th>
                                                    <th>Late Fees (₹)</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($loan->emiSchedules as $emi)
                                                    <tr>
                                                        <td class="text-center">{{ $emi->emi_no }}</td>
                                                        <td class="text-center">{{ \Carbon\Carbon::parse($emi->emi_date)->format('d-m-Y') }}</td>
                                                        <td class="text-center">{{ number_format($emi->amount) }}</td>
                                                        <td class="text-center">{{ number_format($emi->late_fees) }}</td>
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
                    </div>
                @endforeach

            </div>

        @endif
<div class="text-end mt-3">
              <button type="button" class="btn btn-secondary prev">Back</button>
              <button type="button" class="btn btn-primary next">Next</button>
              </div>
    </div>
</div>



            <!-- Step 3: Upload -->
            <div class="card step d-none" id="step-3">
            <div class="card-header">
              <h4>Upload Documents</h4>
            </div>
            <div class="card-body row">
              @foreach (['selfie' => 'Selfie', 'adharcard_front' => 'Aadhar Front', 'adharcard_back' => 'Aadhar Back'] as $name => $label)
          <div class="col-md-4 mb-3">
          <label for="{{ $name }}-input">{{ $label }}</label>
          <div class="position-relative overflow-hidden rounded">
          <img id="preview-{{ $name }}"
          src="{{ $imageUrls[$name] ?? 'https://cdn-icons-png.freepik.com/512/6870/6870041.png' }}"
          alt="{{ $label }} Image" class="w-100 mb-2" style="max-height: 200px; object-fit: contain;">
          </div>
          <input type="file" id="{{ $name }}-input" name="{{ $name }}" class="form-control"
          @if(isset($imageUrls[$name]) && $imageUrls[$name]) disabled @endif>
          </div>
        @endforeach

              <div class="text-end mt-3">
              <button type="button" class="btn btn-secondary prev">Back</button>
              </div>
            </div>
            </div>


          </div>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>

    <!-- jQuery  -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 4 JS (includes Popper.js for modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!--  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const devicePrice = parseFloat(document.getElementById("device_price").value || 0);
      const emiMonths = document.getElementById("emi_months").value;
      const downpayment = parseFloat(document.getElementById("downpayment_dropdown")?.value || 0);

                     if (devicePrice && emiMonths) {
                     // Show downpayment section
                     document.getElementById("downpayment_info")?.classList.remove("d-none");
                     document.getElementById("downpayment_dropdown")?.removeAttribute("disabled");

                     // Calculate and show EMI results
                     calculateEMI(devicePrice, emiMonths, downpayment);
                     }
                     });

                     function calculateEMI(price, monthsText, downpayment = 0) {
                     let months = parseInt(monthsText);
                     let interestRate = 0;

                     switch (monthsText) {
                     case "1 Month": interestRate = 15; months = 1; break;
                     case "2 Month": interestRate = 12.2; months = 2; break;
                     case "3 Month": interestRate = 11; months = 3; break;
                     case "4 Month": interestRate = 11; months = 4; break;
                     default: return;
                     }

                     const loanAmount = price - downpayment;
                     const totalInterest = Math.round((loanAmount * interestRate) / 100);
      const totalPayment = loanAmount + totalInterest;
      const monthlyEMI = Math.round(totalPayment / months);

      document.getElementById("loan_amount_display").textContent = loanAmount;
      document.getElementById("total_interest_display").textContent = totalInterest;
      document.getElementById("total_payment_display").textContent = totalPayment;
      document.getElementById("monthly_emi_display").textContent = monthlyEMI;

      document.getElementById("disburse_amount_input").value = loanAmount;
      document.getElementById("total_interest_input").value = totalInterest;
      document.getElementById("total_payment_input").value = totalPayment;
      document.getElementById("emi_input").value = monthlyEMI;

      document.getElementById("emi_result").classList.remove("d-none");
    }
    </script> -->

    <script>

    const oldBrand = '{{ old('brand_id') }}';
    const oldProduct = '{{ old('product_id') }}';
    if (oldBrand) {
      fetchProducts(oldBrand, oldProduct);
    }


    $(document).ready(function () {
      // Show the modal when the "Show Error Modal" button is clicked
      $('#openModalBtn').on('click', function () {
      $('#errorModal').modal('show');
      });

      // Close the modal when the close button (×) is clicked
      $('#errorModal .close').on('click', function () {
      $('#errorModal').modal('hide');
      });

      // Alternatively, you can manually close the modal after some time
      setTimeout(function () {
      $('#errorModal').modal('hide');
      }, 5000);
    });


    $(document).ready(function () {
      $('#pincode').on('input', function () {
      var pincode = $(this).val().trim();

      console.log("Entered pincode:", pincode);

      if (pincode.length === 6) {

        $.ajax({
        url: '/retailer/check-pincode',
        method: 'GET',
        data: { pincode: pincode },
        success: function (response) {
          if (response.approved) {
          $('.next').prop('disabled', false);
          } else {
          $('.next').prop('disabled', true);
          $('#errorModal').modal('show');
          }
        },
        error: function () {
          alert('An error occurred while checking the pincode.');
        }
        });
      } else {
        $('.next').prop('disabled', true);
      }
      });
    });




    const today = new Date();
    const minDate = new Date(today.setFullYear(today.getFullYear() - 18));
    const minDateString = minDate.toISOString().split('T')[0];
    document.getElementById('date_of_birth').setAttribute('max', minDateString);


    document.getElementById('date_of_birth').addEventListener('change', function () {
      const dob = new Date(this.value);
      const today = new Date();
      const age = today.getFullYear() - dob.getFullYear();
      const month = today.getMonth() - dob.getMonth();

      const nextButton = document.querySelector('.next');
      if (month < 0 || (month === 0 && today.getDate() < dob.getDate())) {
      age--;
      }

      if (age < 18 || (age === 18 && month < 0)) {
      document.getElementById('dobError').style.display = 'block';
      this.setCustomValidity('You must be at least 18 years old.');

      const errorPopup = document.createElement('div');
      errorPopup.classList.add('error-popup');
      errorPopup.innerText = 'You must be at least 18 years old.';
      document.body.appendChild(errorPopup);

      setTimeout(() => {
        errorPopup.remove();
      }, 4000);


      nextButton.disabled = true;
      } else {
      document.getElementById('dobError').style.display = 'none';
      this.setCustomValidity('');

      nextButton.disabled = false;
      }
    });



    let currentStep = 1;

    function showStep(step) {
      $('.step').addClass('d-none');
      $('#step-' + step).removeClass('d-none');
    }

    $('.next').on('click', function () {
      let valid = true;
      const currentCard = $('#step-' + currentStep);

      currentCard.find('input, select').each(function () {
      if ($(this).prop('required') && !$(this).val()) {
        $(this).addClass('is-invalid');
        valid = false;
      } else {
        $(this).removeClass('is-invalid');
      }

      if ($(this).attr('type') === 'file' && $(this).prop('required') && this.files.length === 0) {
        $(this).addClass('is-invalid');
        valid = false;
      }
      });

      if (valid) {
      if (currentStep < 4) currentStep++;
      showStep(currentStep);
      }
    });

    $('.prev').on('click', function () {
      if (currentStep > 1) {
      currentStep--;
      showStep(currentStep);
      }
    });

    $('input, select').on('input change', function () {
      if ($(this).val()) {
      $(this).removeClass('is-invalid');
      }
    });

    showStep(currentStep);



    $('#product_id').prop('disabled', true);
    $('#brand_id').on('change', function () {
      let brandId = $(this).val();
      $('#product_id').prop('disabled', true).html('<option value="">Loading...</option>');

      if (brandId) {
      $.ajax({
        url: '/retailer/get-products/' + brandId,
        type: 'GET',
        success: function (products) {
        let options = '<option value="">Select Product</option>';
        $.each(products, function (key, product) {
          options += '<option value="' + product.id + '">' + product.name + '</option>';
        });
        $('#product_id').html(options).prop('disabled', false);
        }
      });
      } else {
      $('#product_id').html('<option value="">Select Product</option>').prop('disabled', true);
      }
    });


    $('#state_id').on('change', function () {
      let stateID = $(this).val();
      $('#city_id').prop('disabled', true).html('<option value="">Loading...</option>');

      if (stateID) {
      $.ajax({
        url: "/retailer/get-cities/" + stateID,

        type: "GET",
        success: function (res) {
        let options = '<option value="">Select City</option>';
        $.each(res, function (key, value) {
          options += '<option value="' + value.id + '">' + value.name + '</option>';
        });
        $('#city_id').html(options).prop('disabled', false);
        },
        error: function (xhr) {
        console.error("City load failed:", xhr.responseText);
        $('#city_id').html('<option value="">Failed to load cities</option>').prop('disabled', true);
        }
      });
      }
    });

    function formatAadhar(input) {
      let value = input.value.replace(/\D/g, '');
      let formatted = value.match(/.{1,4}/g)?.join(' ') || '';
      input.value = formatted;
    }

    </script>

  </body>
@endsection