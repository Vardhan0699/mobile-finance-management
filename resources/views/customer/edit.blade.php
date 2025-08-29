@extends('retailerLogin.layout.layout')

@section('content')

<body class="theme-1">
  <form action="" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="page-content-wrapper">
      <div class="content-container">
        <div class="page-content">
          <div class="content-header">
            <h1>Update Customer</h1>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item">Customer</li>
              <li class="breadcrumb-item">Update Customer</li>
            </ul>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  <div id="step-container">
                    <!-- Step 1: Customer Info -->
                    <div class="card step" id="step-1">
                      <div class="card-header"><h4>Customer Information</h4></div>
                      <div class="card-body row">
                        @php $retailerId = session('retailer_id'); @endphp
                        <input type="hidden" name="retailer_id" value="{{ $retailerId }}">

                        <div class="col-md-6 mb-3">
                          <label>First Name</label>
                          <input type="text" name="customer_firstname" class="form-control" value="{{ old('customer_firstname', $customers->customer_firstname ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                          <label>Last Name</label>
                          <input type="text" name="customer_lastname" class="form-control" value="{{ old('customer_lastname', $customers->customer_lastname ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                          <label>Date of Birth</label>
                          <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $customers->date_of_birth ?? '') }}" >
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Father's Name</label>
                          <input type="text"  name="father_name" class="form-control" value="{{ old('father_name', $customers->father_name ?? '') }}" >
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Address 1</label>
                          <input type="text" name="address1" class="form-control" value="{{ old('address1', $customers->address1 ?? '') }}" >
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Address 2</label>
                          <input type="text" name="address2" class="form-control" value="{{ old('address2', $customers->address2 ?? '') }}" >
                        </div>

                        <div class="col-md-4 mb-3">
                          <label>Nearby</label>
                          <input type="text" name="nearby" class="form-control" value="{{ old('nearby', $customers->nearby ?? '') }}" >
                        </div>

                        <div class="col-md-4 mb-3">
                          <label>Village</label>
                          <input type="text" name="village" class="form-control" value="{{ old('village', $customers->village ?? '') }}" >
                        </div>

                        <div class="col-md-4 mb-3">
                          <label>State</label>
                          <select name="state_id" id="state_id" class="form-select" value="{{ old('state_id', $customers->state_id ?? '') }}" >
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id', $customers->state_id ?? '') == $state->id ? 'selected' : '' }} >{{ $state->name }}</option>
                            @endforeach
                          </select>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>City</label>
                          <select name="city_id" id="city_id" class="form-select" >
                            <option value="">Select City</option>
                            @foreach ($cities as $city)
                            <option value="{{ $city->id }}" 
                                    {{ old('city_id', $customers->city_id ?? '') == $city->id ? 'selected' : '' }}>
                              {{ $city->name }}
                            </option>
                            @endforeach
                          </select>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Pincode</label>
                          <input type="text" name="pincode" id="pincode" class="form-control" maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6);" value="{{ old('pincode', $customers->pincode ?? '') }}" >
                        </div>

                        <div class="col-md-6 mb-3">
                          <label for="aadhaar">Aadhaar Number</label>
                          <input type="text" name="aadhaar_number" id="aadhaar" maxlength="12" 
                                 class="form-control" value="{{ old('aadhar_number', $customers->aadhar_number ?? '') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '');" >
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Mobile</label>
                          <input type="text" name="mobile" id="mobileInput" maxlength="10"
                                 class="form-control"
                                 oninput="this.value = this.value.replace(/[^0-9]/g, ''); checkMobileExistence(); validateMobile(this, 'mobileError');" value="{{ old('mobile', $customers->mobile ?? '') }}" >
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Alternate Mobile</label>
                          <input type="text" name="alternate_mobile" id="alternate_mobile" 
                                 class="form-control" 
                                 maxlength="10" minlength="10" 
                                 pattern="\d{10}" 
                                 oninput="this.value = this.value.replace(/[^0-9]/g, '');
                                          validateMobile(this, 'altMobileError');" value="{{ old('alternate_mobile', $customers->alternate_mobile ?? '') }}" >

                        </div>

                        <div class="text-end"><button type="button" class="btn btn-primary next">Next</button></div>
                      </div>
                    </div>

                    @php
                    $devicePrice = old('sell_price', $customers->sell_price ?? '');
                    $emiMonths = old('months', $customers->months ?? '');
                    $downpayment = old('downpayment', $customers->downpayment ?? '');
                    $totalInterest = old('total_interest', $customers->total_interest ?? '');
                    $totalPayment = old('total_payment', $customers->total_payment ?? '');
                    $emi = old('emi', $customers->emi ?? '');
                    $disburseAmount = old('disburse_amount', $customers->disburse_amount ?? '');
                    @endphp

                    <div class="card step" id="step-2">
                      <div class="card-header"><h4>EMI Details</h4></div>
                      <div class="card-body row">
                        <div class="col-md-6 mb-3">
                          <label>Device Price (in ₹)</label>
                          <input type="number" id="device_price" name="sell_price" class="form-control" value="{{ $devicePrice }}" disabled>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>EMI Duration (Months)</label>
                          <select id="emi_months" name="months" class="form-select" disabled>
                            <option value="">Select Duration</option>
                            <option value="1 Month" {{ $emiMonths == '1 Month' ? 'selected' : '' }}>1 Month (15%)</option>
                            <option value="2 Month" {{ $emiMonths == '2 Month' ? 'selected' : '' }}>2 Months (12.2%)</option>
                            <option value="3 Month" {{ $emiMonths == '3 Month' ? 'selected' : '' }}>3 Months (11%)</option>
                            <option value="4 Month" {{ $emiMonths == '4 Month' ? 'selected' : '' }}>4 Months (11%)</option>
                          </select>
                        </div>

                        <div class="col-md-6 mb-3 {{ $downpayment ? '' : 'd-none' }}" id="downpayment_info">
                          <label>Down Payment</label>
                          <input type="number" id="device_price" name="sell_price" class="form-control" value="{{ $downpayment }}" disabled>
                          <div class="mt-2 text-muted">
                            Min. Down Payment Required: ₹<span id="min_downpayment_display">{{ $downpayment ?: 0 }}</span>
                          </div>
                        </div>


                        <div id="emi_result" class="row col-md-6 p-4 {{ $emi ? '' : 'd-none' }}">
                          <h5>EMI Details</h5>
                          <p><strong>Loan Amount:</strong> ₹<span id="loan_amount_display">{{ $disburseAmount }}</span></p>
                          <p><strong>Total Interest:</strong> ₹<span id="total_interest_display">{{ $totalInterest }}</span></p>
                          <p><strong>Total Payment:</strong> ₹<span id="total_payment_display">{{ $totalPayment }}</span></p>
                          <p><strong>Monthly EMI:</strong> ₹<span id="monthly_emi_display">{{ $emi }}</span></p>
                        </div>

                        <!-- Device Details Section -->
                        <div class="col-md-6 mb-3">
                          <label>Brand</label>
                          <select name="brand_id" id="brand_id" class="form-select" disabled>
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" 
                                    @if (old('brand_id', $customers->brand_id) == $brand->id) selected @endif>
                              {{ $brand->brand_name }}
                            </option>
                            @endforeach
                          </select>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Product</label>
                          <select name="product_id" id="product_id" class="form-select" disabled>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    @if (old('product_id', $customers->product_id) == $product->id) selected @endif>
                              {{ $product->product_name }}
                            </option>
                            @endforeach
                          </select>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>IMEI 1</label>
                          <input type="number" name="imei1" class="form-control" value="{{ $customers->imei1 }}" disabled>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>IMEI 2</label>
                          <input type="number" name="imei2" class="form-control" value="{{ $customers->imei2 }}" disabled>
                        </div>

                        <input type="hidden" name="disburse_amount" id="disburse_amount_input" value="{{ $disburseAmount }}">
                        <input type="hidden" name="emi" id="emi_input" value="{{ $emi }}">
                        <input type="hidden" name="total_interest" id="total_interest_input" value="{{ $totalInterest }}">
                        <input type="hidden" name="total_payment" id="total_payment_input" value="{{ $totalPayment }}">

                        <div class="text-end">
                          <button type="button" class="btn btn-secondary prev">Back</button>
                          <button type="button" class="btn btn-primary next">Next</button>
                        </div>
                      </div>
                    </div>

                    <!-- Step 4: Upload -->
                    <!-- <div class="card step d-none" id="step-4">
<div class="card-header"><h4>Upload Documents</h4></div>
<div class="card-body row">
@foreach (['selfie' => 'Selfie', 'adharcard_front' => 'Aadhar Front', 'adharcard_back' => 'Aadhar Back'] as $name => $label)
<div class="col-md-4 mb-3">
<label>{{ $label }}</label>
<div class="position-relative overflow-hidden rounded">
<img src="https://cdn-icons-png.freepik.com/512/6870/6870041.png" alt="image"
class="w-100 mb-2" style="max-height: 200px; object-fit: cover;">
</div>
<input type="file" name="{{ $name }}" class="form-control">
</div>
@endforeach
<div class="text-end mt-3">
<button type="button" class="btn btn-secondary prev">Back</button>
<button type="submit" class="btn btn-success">Submit</button>
</div>
</div>
</div>  -->


                    <!-- Step 4: Upload -->
                    <div class="card step d-none" id="step-4">
                      <div class="card-header"><h4>Upload Documents</h4></div>
                      <div class="card-body row">
                        @foreach (['selfie' => 'Selfie', 'adharcard_front' => 'Aadhar Front', 'adharcard_back' => 'Aadhar Back'] as $name => $label)
                        <div class="col-md-4 mb-3">
                          <label for="{{ $name }}-input">{{ $label }}</label>
                          <div class="position-relative overflow-hidden rounded">
                            <img id="preview-{{ $name }}"
                                 src="{{ $imageUrls[$name] ?? 'https://cdn-icons-png.freepik.com/512/6870/6870041.png' }}"
                                 alt="{{ $label }} Image"
                                 class="w-100 mb-2"
                                 style="max-height: 200px; object-fit: contain;">
                          </div>
                          <input type="file"
                                 id="{{ $name }}-input"
                                 name="{{ $name }}"
                                 class="form-control"
                                 @if(isset($imageUrls[$name]) && $imageUrls[$name]) disabled @endif>
                        </div>
                        @endforeach

                        <div class="text-end mt-3">
                          <button type="button" class="btn btn-secondary prev">Back</button>
                          <button type="submit" class="btn btn-success">Submit</button>
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
  </form>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap 4 JS (includes Popper.js for modals) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const imageFields = ['selfie', 'adharcard_front', 'adharcard_back'];

      imageFields.forEach(field => {
        const input = document.getElementById(`${field}-input`);
        const preview = document.getElementById(`preview-${field}`);

        if (input && preview && !input.disabled) {
          input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
              const reader = new FileReader();
              reader.onload = function (e) {
                preview.src = e.target.result;
              };
              reader.readAsDataURL(file);
            }
          });
        }
      });
    });
  </script>
  <script>


    $(document).ready(function() {
      $('#pincode').on('input', function() {
        var pincode = $(this).val().trim();
        if (pincode.length === 6) {
          $.ajax({
            url: '/retailer/check-pincode',
            method: 'GET',
            data: { pincode: pincode },
            success: function(response) {
              if (response.approved) {
                $('.next').prop('disabled', false);
              } else {
                $('.next').prop('disabled', true);
                $('#errorModal').modal('show');
              }
            },
            error: function() {
              alert('An error occurred while checking the pincode.');
            }
          });
        }else {
          $('.next').prop('disabled', true);
        }
      });
    });

    document.getElementById('aadhaar').addEventListener('input', function() {
      let aadharNumber = this.value;

      // Validate Aadhaar number (optional format check)
      if (aadharNumber.length === 12 && /^\d+$/.test(aadharNumber)) {
        // Make AJAX request to check if Aadhaar exists
        fetch('{{ route('retailer.check_aadhar') }}', {
              method: 'POST',
              headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              },
              body: JSON.stringify({ aadhar_number: aadharNumber })
      })
      .then(response => response.json())
      .then(data => {
        let errorElement = document.getElementById('aadharError');
        let nextButton = document.querySelector('.next'); // Assuming .next is your next button

        if (data.exists) {
          // Show error modal and disable Next button
          $('#aadhaarErrorModal').modal('show');
          nextButton.disabled = true;
        } else {
          // Hide error and enable Next button
          errorElement.style.display = 'none';
          nextButton.disabled = false;
        }
      })
      .catch(error => console.error('Error:', error));
    } else {

                                                        let errorElement = document.getElementById('aadharError');
    errorElement.style.display = 'block';
    errorElement.textContent = 'Invalid Aadhaar Number';
    document.querySelector('.next').disabled = true;
    }
    });

    function checkMobileExistence() {
      const mobile = document.getElementById('mobileInput').value;
      const mobileError = document.getElementById('mobileError');
      const nextButton = document.querySelector('.next');

      if (!/^[6-9]\d{9}$/.test(mobile)) {
        mobileError.innerText = "Invalid Mobile Number";
        mobileError.style.display = 'block';
        nextButton.disabled = true;
        return;
      }

      // Make AJAX call
      fetch(`/retailer/check-customer-mobile?mobile=${mobile}`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {

        if (data.exists) {
          mobileError.innerText = "Mobile number already exists.";
          mobileError.style.display = 'block';
          nextButton.disabled = true;
        } else {
          mobileError.style.display = 'none';
          nextButton.disabled = false;
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
      });
    }

    function validateMobile(input, errorId) {
      const errorEl = document.getElementById(errorId);
      if (/^[6-9]\d{0,9}$/.test(input.value)) {
        errorEl.style.display = 'none';
      } else {
        errorEl.innerText = "Invalid Mobile Number";
        errorEl.style.display = 'block';
      }
    }



    const today = new Date();
    const minDate = new Date(today.setFullYear(today.getFullYear() - 18));
    const minDateString = minDate.toISOString().split('T')[0];
    document.getElementById('date_of_birth').setAttribute('max', minDateString);


    document.getElementById('date_of_birth').addEventListener('change', function() {
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



  </script>



</body>
@endsection
