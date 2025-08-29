@extends('retailerLogin.layout.layout')

@section('content')
<style>
  .error-popup {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #f44336;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    z-index: 9999;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .select2-container--bootstrap-5 .select2-selection {
    min-height: 30px !important; /* Standard Bootstrap input height */
    padding: 4px 12px;
    font-size: 1rem;
  }
  
  .select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #aaa;
    border-radius: 5px;
    height: 36px;
}

</style>

<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Add Customer / New Loan</h1>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Customer</li>
            <li class="breadcrumb-item">Add Customer</li>
          </ul>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12">
                <div id="step-container">
                  <form action="{{ route('retailer.customerStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Step 0: Customer Search (UPDATED) -->
                    <div class="card step" id="step-0">
                      <div class="card-header">
                        <h4>Search Existing Customer</h4>
                      </div>
                      <div class="card-body">
                        <div class="row">

                          <!-- Search by Aadhaar -->
                          <div class="col-md-6 mb-3">
                            <label>Search by Aadhaar Number</label>
                            <div class="input-group">
                              <input type="text" id="searchAadhaar" class="form-control" maxlength="12"
                                     oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                     placeholder="Enter Aadhaar Number to search">
                              <button type="button" class="btn btn-primary"
                                      onclick="searchCustomer('aadhaar')">Search</button>
                            </div>
                          </div>

                          <!-- Search by Mobile -->
                          <div class="col-md-6 mb-3">
                            <label>Search by Mobile Number</label>
                            <div class="input-group">
                              <input type="text" id="searchMobile" class="form-control" maxlength="10"
                                     oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                     placeholder="Enter Mobile Number to search">
                              <button type="button" class="btn btn-primary"
                                      onclick="searchCustomer('mobile')">Search</button>
                            </div>
                          </div>

                        </div>

                        <!-- Search Results -->
                        <div id="searchResults" class="mt-3" style="display: none;">
                          <div class="alert alert-info">
                            <h5>Customer Found!</h5>
                            <p><strong>Name:</strong> <span id="foundCustomerName"></span></p>
                            <p><strong>Mobile:</strong> <span id="foundCustomerMobile"></span></p>
                            <p><strong>Active Loans:</strong> <span id="foundCustomerLoans"></span></p>
                            <button type="button" class="btn btn-success" onclick="useExistingCustomer()">Add New Loan for
                              This Customer</button>
                          </div>
                        </div>

                        <div class="text-center mt-4">
                          <button type="button" class="btn btn-outline-primary" onclick="skipToNewCustomer()">Skip - Add
                            New Customer</button>
                        </div>
                      </div>
                    </div>

                    <!-- Step 1: Customer Info -->
                    @php $retailerId = session('retailer_id'); @endphp
                    <input type="hidden" name="retailer_id" value="{{ $retailerId }}">
                    <input type="hidden" name="is_existing_customer" id="isExistingCustomer" value="0">
                    <input type="hidden" name="existing_customer_id" id="existingCustomerId" value="">

                    <div class="card step d-none" id="step-1">
                      <div class="card-header">
                        <h4>Customer Information</h4>
                        <div id="existingCustomerBadge" class="badge bg-success" style="display: none;">Adding loan for
                          existing customer</div>
                      </div>
                      <div class="card-body row">
                        @php $retailerId = session('retailer_id'); @endphp
                        <input type="hidden" name="retailer_id" value="{{ $retailerId }}">
                        <input type="hidden" name="is_existing_customer" id="isExistingCustomer" value="0">

                        <div class="col-md-4 mb-3">
                          <label>Pincode <span class="text-danger">*</span></label>
                          <input type="text" name="pincode" id="pincode" class="form-control" maxlength="7"
                                 onblur="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6)"
                                 oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6);"
                                 placeholder="Enter Pincode Digits" required>
                          <small id="pincodeError" style="color: red; display: none;">Invalid Pincode Number</small>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label for="aadhaar">Aadhaar Number <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <input type="text" name="aadhaar_number" id="aadhaar" maxlength="12" class="form-control"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Enter Aadhaar Number"
                                   required>
                            <button type="button" class="btn btn-primary" id="verifyAadhaarBtn">Verify</button>
                          </div>
                          <small id="aadharError" style="color: red; display: none;"></small>
                        </div>

                        <!-- Success Message Block -->
                        <div id="aadhaarVerifiedMsg" class="alert alert-success" style="display: none;">
                          ✅ Aadhaar has been successfully verified!
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>First Name <span class="text-danger">*</span></label>
                          <input type="text" name="customer_firstname" id="customer_firstname" class="form-control"
                                 maxlength="15" placeholder="Enter Firstname" required>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Last Name <span class="text-danger">*</span></label>
                          <input type="text" name="customer_lastname" id="customer_lastname" class="form-control"
                                 maxlength="15" placeholder="Enter Lastname" required>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Date of Birth <span class="text-danger">*</span></label>
                          <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" required>
                          <small id="dobError" style="color: red; display: none;">You must be at least 18 years
                            old.</small>
                          <div id="formattedDOB" style="margin-top: 5px; color: green; display:none;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Father's Name <span class="text-danger">*</span></label>
                          <input type="text" name="father_name" id="father_name" class="form-control"
                                 placeholder="Enter Father's name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label for="address1">Address 1 <span class="text-danger">*</span></label>
                          <input type="text" name="address1" id="address1" class="form-control"
                                 placeholder="Enter Current Address" required>
                          <small id="addressError" class="text-danger d-none">Address must be at least 20 words.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Alternate Address (Optional)</label>
                          <input type="text" name="address2" id="address2" class="form-control"
                                 placeholder="Enter Alternate Address">
                        </div>

                        <!-- State Dropdown -->
                        <div class="col-md-4 mb-3">
                          <label>State <span class="text-danger">*</span></label>
                          <select name="state_id" id="state_id" class="form-select" required>
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                            <option value="{{ $state->id }}" {{ $state->name == 'Uttar Pradesh' ? 'selected' : '' }}>
                              {{ $state->name }}
                            </option>
                            @endforeach
                          </select>
                        </div>

                        <!-- City Dropdown -->
                        <div class="col-md-4 mb-3">
                          <label>City <span class="text-danger">*</span></label>
                          <select name="city_id" id="city_id" class="form-select" required disabled>
                            <option value="">Select City</option>
                          </select>
                        </div>

                        <div class="col-md-4 mb-3">
                          <label>Post <span class="text-danger">*</span></label>
                          <input type="text" name="post" id="post" class="form-control" placeholder="Enter Post" required>
                        </div>

                        <div class="col-md-4 mb-3">
                          <label>Village <span class="text-danger">*</span></label>
                          <input type="text" name="village" id="village" class="form-control" placeholder="Enter Village"
                                 required>
                        </div>

                        <div class="col-md-4 mb-3">
                          <label>Nearby <span class="text-danger">*</span></label>
                          <input type="text" name="nearby" id="nearby" class="form-control" placeholder="Enter Nearby"
                                 required>
                        </div>

                        <div class="col-md-4 mb-3">
                          <label>Tola/Mohalla <span class="text-danger">*</span></label>
                          <input type="text" name="mohalla" id="mohalla" class="form-control"
                                 placeholder="Enter Tola/Mohalla" required>
                        </div>

                        <!-- Mobile Input -->
                        <div class="col-md-6 mb-3">
                          <div class="col-md-12 mb-3">
                            <label>Mobile <span class="text-danger">*</span></label>
                            <div class="input-group">
                              <input type="text" name="mobile" id="mobileInput" maxlength="10" class="form-control"
                                     oninput="this.value = this.value.replace(/[^0-9]/g, ''); validateMobileNumbers();"
                                     placeholder="Enter Mobile Number" required>
                              <button type="button" class="btn btn-sm btn-primary" onclick="sendOTP()">Send OTP</button>
                            </div>
                            <small id="mobileError" style="color: red; display: none;"></small>
                          </div>

                          <!-- OTP Verification Section -->
                          <div class="col-md-6 mb-3" id="otpSection" style="display:none;">
                            <label>Enter Mobile OTP</label>
                            <input type="text" id="otpInput" class="form-control" maxlength="6">
                            <button type="button" class="btn btn-success mt-2" onclick="verifyOTP()">Verify OTP</button></br>
                            <small class="col-md-12 mb-3" id="otpMessage" style="color: green;"></small>
                          </div>

                          <!-- Hidden input to track verification -->
                          <input type="hidden" name="mobile_verified" id="mobileVerifiedInput" value="0">
                          <div id="recaptcha-container"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Alternate Mobile <span class="text-danger">*</span></label>
                          <input type="text" name="alternate_mobile" id="alternate_mobile" class="form-control"
                                 maxlength="10" minlength="10" pattern="[6-9]{1}[0-9]{9}"
                                 oninput="this.value = this.value.replace(/[^0-9]/g, ''); validateMobileNumbers();"
                                 placeholder="Enter Alternate Mobile" required>
                          <small id="altMobileError" style="color: red; display: none;"></small>
                        </div>

                        <div class="text-end">
                          <button type="button" class="btn btn-secondary" onclick="goBackToSearch()">Back to
                            Search</button>
                          <button type="button" class="btn btn-primary next">Next</button>
                        </div>
                      </div>
                    </div>

                    <!-- Step 2: EMI and Device Details -->
                    <div class="card step d-none" id="step-2">
                      <div class="card-header">
                        <h4>EMI and Device Details</h4>
                      </div>
                      <div class="card-body row">

                        <!-- Device Details Section -->
                        <div class="col-md-6 mb-3">
                          <label>Brand <span class="text-danger">*</span></label>
                          <select name="brand_id" id="brand_id" class="form-select" required>
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                            @endforeach
                          </select>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label>Product <span class="text-danger">*</span></label>
                          <select name="product_id" id="product_id" class="form-select" required disabled>
                            <option value="">Select Product</option>
                          </select>
                        </div>

                        <input type="hidden" id="product_price">

                        <!-- IMEI Fields -->
                        <div class="col-md-6 mb-3">
                          <label>IMEI 1 <span class="text-danger">*</span></label>
                          <input type="number" name="imei1" class="form-control" maxlength="15" pattern="\d{15}"
                                 placeholder="Enter IMEI Number"
                                 oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,15)" required>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label>IMEI 2 <span class="text-danger">*</span></label>
                          <input type="number" name="imei2" class="form-control" maxlength="15" pattern="\d{15}"
                                 placeholder="Enter IMEI Number"
                                 oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,15)" required>
                        </div>

                        <!-- Device Price -->
                        <div class="col-md-6 mb-3">
                          <label for="sell_price" class="form-label">Disbursement Amount (in ₹) <span
                                                                                                      class="text-danger">*</span></label>
                          <input type="number" id="device_price" name="sell_price" class="form-control"
                                 placeholder="Enter Device Price" required>
                        </div>

                        <!-- EMI Duration -->
                        <div class="col-md-6 mb-3">
                          <label for="emi_months" class="form-label">EMI Duration (Months) <span
                                                                                                 class="text-danger">*</span></label>
                          <select id="emi_months" name="months" class="form-select" required>
                            <option value="">Select Duration</option>
                            <option value="1">1 Month</option>
                            <option value="2">2 Months</option>
                            <option value="3">3 Months</option>
                            <option value="4">4 Months</option>
                          </select>
                        </div>

                        <!-- Down Payment Dropdown -->
                        <div class="col-md-6 mb-3 d-none" id="downpayment_info">
                          <label for="downpayment_dropdown" class="form-label">Select Down Payment <span
                                                                                                         class="text-danger">*</span></label>
                          <select id="downpayment_dropdown" name="downpayment" class="form-select">
                            <option value="">Select Down Payment</option>
                          </select>
                          <div class="mt-2 text-muted">
                            Min. Down Payment Required: ₹<span id="min_downpayment_display">0</span>
                          </div>
                        </div>

                        <!-- EMI Calculation Result -->
                        <div id="emi_result" class="row col-md-6 p-4 d-none">
                          <h5>EMI Details</h5>
                          <p><strong>Monthly EMI:</strong> ₹<span id="monthly_emi_display">0</span></p>
                        </div>

                        <!-- Hidden Inputs -->
                        <input type="hidden" name="disburse_amount" id="disburse_amount_input">
                        <input type="hidden" name="emi" id="emi_input">
                        <input type="hidden" name="total_interest" id="total_interest_input">
                        <input type="hidden" name="total_payment" id="total_payment_input">

                        <div class="text-end">
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
                        <div class="alert alert-info" id="existingDocumentsNote" style="display: none;">
                          <i class="fas fa-info-circle"></i> Documents from previous application will be used. Upload new
                          ones only if needed.
                        </div>

                        @foreach (['selfie' => 'Selfie', 'adharcard_front' => 'Aadhar Front', 'adharcard_back' => 'Aadhar Back'] as $name => $label)
                        <div class="col-md-4 mb-3">
                          <label>{{ $label }} <span class="text-danger" id="required-{{ $name }}">*</span></label>
                          <div class="position-relative overflow-hidden rounded">
                            <img id="preview-{{ $name }}" src="https://cdn-icons-png.freepik.com/512/6870/6870041.png"
                                 alt="image" class="w-100 mb-3" style="max-height: 200px; object-fit: contain;">
                          </div>
                          <input type="file" name="{{ $name }}" id="file-{{ $name }}" class="form-control"
                                 onchange="previewImage(event, 'preview-{{ $name }}')" required>
                        </div>
                        @endforeach

                        <div class="text-end mt-3">
                          <button type="button" class="btn btn-secondary prev">Back</button>
                          <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div id="errorModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Error</h5>
          <!-- Close Button -->
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Pincode is not approved. Please check and try again.</p>
        </div>

      </div>
    </div>
  </div>

  <!-- Aadhaar Error Modal -->
  <div id="aadhaarErrorModal" role="dialog" class="modal fade" tabindex="-1" aria-labelledby="modalLabel"
       aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" bg-danger text-white>
          <h5 class="modal-title" id="modalLabel">Error</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Aadhaar number is already exist. Please check and try again.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="mobileExistsModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalLabel">Mobile Exists</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          This mobile number is already registered!
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
</body>

<!-- Bootstrap JS (needed for dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 4 JS (includes Popper.js for modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (if not already included) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const mobileInput = document.getElementById('mobileInput');
    const altMobileInput = document.getElementById('alternate_mobile');
    const nextButton = document.querySelector('.next'); // adjust selector if needed

    function validateMobileNumbers(event = null) {
      const mobile = mobileInput.value.trim();
      const altMobile = altMobileInput.value.trim();

      if (mobile.length === 10 && altMobile.length === 10) {
        if (mobile === altMobile) {
          if (event) event.preventDefault();

          Swal.fire({
            icon: 'error',
            title: 'Mobile Conflict',
            text: 'Alternate mobile number must be different from primary mobile number.',
          }).then(() => {
            altMobileInput.value = '';
            altMobileInput.focus();
            if (nextButton) nextButton.disabled = true;
          });

          return false;
        } else {
          if (nextButton) nextButton.disabled = false;
        }
      } else {
        if (nextButton) nextButton.disabled = true;
      }

      return true;
    }

    mobileInput.addEventListener('input', () => validateMobileNumbers());
    altMobileInput.addEventListener('input', () => validateMobileNumbers());

    if (nextButton) nextButton.disabled = true;
  });


  document.addEventListener('DOMContentLoaded', function () {
    const villageInput = document.querySelector('input[name="village"]');
    const mohallaInput = document.querySelector('input[name="mohalla"]');

    const showError = () => {
      const errorId = "villageMohallaError";
      let errorEl = document.getElementById(errorId);

      if (villageInput.value.trim() && mohallaInput.value.trim() &&
          villageInput.value.trim().toLowerCase() === mohallaInput.value.trim().toLowerCase()) {

        if (!errorEl) {
          errorEl = document.createElement("small");
          errorEl.id = errorId;
          errorEl.style.color = "red";
          errorEl.textContent = "Village and Mohalla should not be the same.";
          mohallaInput.parentElement.appendChild(errorEl);
        }
      } else {
        if (errorEl) {
          errorEl.remove();
        }
      }
    };

    villageInput.addEventListener('input', showError);
    mohallaInput.addEventListener('input', showError);
  });


  const addressInput = document.getElementById('address1');
  const errorMsg = document.getElementById('addressError');

  addressInput.addEventListener('input', function () {
    const wordCount = addressInput.value.trim().split(/\s+/).filter(Boolean).length;

    if (wordCount < 3) {
      errorMsg.classList.remove('d-none');
    } else {
      errorMsg.classList.add('d-none');
    }
  });



  document.querySelector('.next').addEventListener('click', function () {
    const currentStep = document.querySelector('.step:not(.d-none)');
    const requiredInputs = currentStep.querySelectorAll('input[required], select[required], textarea[required]');
    let firstInvalid = null;

    requiredInputs.forEach(input => {
      if (!input.value.trim()) {
        // Mark field as invalid visually
        input.classList.add('is-invalid');

        // Show error messages if needed
        const errorField = input.nextElementSibling;
        if (errorField && errorField.tagName === 'SMALL') {
          errorField.style.display = 'block';
        }

        // Focus the first invalid field
        if (!firstInvalid) {
          firstInvalid = input;
        }
      } else {
        // Clear invalid styling if field is valid
        input.classList.remove('is-invalid');
        const errorField = input.nextElementSibling;
        if (errorField && errorField.tagName === 'SMALL') {
          errorField.style.display = 'none';
        }
      }
    });

    if (firstInvalid) {
      firstInvalid.focus();
    } else {
      // All fields valid, proceed to next step
      const nextStep = currentStep.nextElementSibling;
      if (nextStep && nextStep.classList.contains('step')) {
        currentStep.classList.add('d-none');
        nextStep.classList.remove('d-none');
        window.scrollTo(0, 0);
      }
    }
  });
</script>


<!-- Firebase SDKs (only once) -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-auth.js"></script>

<script>
  // ✅ Initialize Firebase App (only ONCE)
  const firebaseConfig = {
    apiKey: "AIzaSyAGQjdT1nUqq0FWTzMiGrI6jVE-dRg48Oo",
    authDomain: "vardanindia.org",
    projectId: "retailerotp-f39d9",
    storageBucket: "retailerotp-f39d9.appspot.com",
    messagingSenderId: "527486838285",
    appId: "1:527486838285:web:d6eb5f97b250d0645a9744",
    measurementId: "G-6GEHJ70QE8"
  };

  // ✅ Check if not already initialized
  if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
  }

  const auth = firebase.auth();
  let confirmationResult = null;

  // ✅ Set up invisible reCAPTCHA after DOM loads
  window.onload = function () {
    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
      size: 'invisible',
      callback: function (response) {
        // CAPTCHA solved
      }
    });
    recaptchaVerifier.render();
  };

  function validateMobile(input) {
    const mobile = input.value.trim();
    const errorEl = document.getElementById('mobileError');
    // Allow only if it starts with 6-9 and has 10 digits
    const validPattern = /^[6-9][0-9]{9}$/;

    if (!validPattern.test(mobile)) {
      errorEl.textContent = "Enter a valid 10-digit mobile number starting with 6-9.";
      errorEl.style.display = "block";
    } else {
      errorEl.style.display = "none";
    }
  }

  function sendOTP() {
    const mobile = document.getElementById('mobileInput').value.trim();
    const fullPhone = '+91' + mobile;
    const errorEl = document.getElementById('mobileError');

    if (mobile.length !== 10 || isNaN(mobile)) {
      errorEl.textContent = "Please enter a valid 10-digit number.";
      errorEl.style.display = "block";
      return;
    }

    auth.signInWithPhoneNumber(fullPhone, window.recaptchaVerifier)
    .then(function (result) {
      confirmationResult = result;
      document.getElementById('otpSection').style.display = 'block';
      errorEl.style.display = "none";
      Swal.fire({
        icon: 'success',
        title: 'OTP Sent',
        text: `OTP sent to ${fullPhone}`,
        timer: 3000,
        showConfirmButton: false
      });
    })
    .catch(function (error) {
      errorEl.textContent = error.message;
      errorEl.style.display = "block";
    });
  }

  function verifyOTP() {
    const otp = document.getElementById('otpInput').value.trim();
    const otpMessage = document.getElementById('otpMessage');
    const mobile = document.getElementById('mobileInput').value.trim();

    if (!confirmationResult) {
      Swal.fire({
        icon: 'warning',
        title: 'OTP Not Sent',
        text: 'Please send OTP first',
      });
      return;
    }

    confirmationResult.confirm(otp)
    .then(function (result) {
      otpMessage.textContent = "Phone number verified successfully!";
      otpMessage.style.color = "green";
      document.getElementById('mobileVerifiedInput').value = "1";

      // ✅ SweetAlert for success
      Swal.fire({
        icon: 'success',
        title: 'Verified',
        text: 'Phone number has been verified successfully!',
        timer: 3000,
        showConfirmButton: false
      });

      fetch(`{{ route('retailer.mobile.verify') }}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': `{{ csrf_token() }}`
        },
        body: JSON.stringify({ phone: '+91' + mobile })
      })
      .then(response => response.json())
      .then(data => console.log(data.message))
      .catch(err => console.error(err));
    })
    .catch(function (error) {
      otpMessage.textContent = "Invalid OTP";
      otpMessage.style.color = "red";
      Swal.fire({
        icon: 'error',
        title: 'Invalid OTP',
        text: 'Please enter the correct OTP.',
      });
    });
  }


  // Function to update the image preview
  function previewImage(event, name) {
    var reader = new FileReader();
    reader.onload = function () {
      var preview = document.getElementById('preview-image-' + name);
      preview.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
  }

  $(document).ready(function () {
    $('.next').prop('disabled', true); // Disable next button initially

    $('#verifyAadhaarBtn').on('click', function () {
      const aadhaarNumber = $('#aadhaar').val().trim();

      if (aadhaarNumber.length !== 12 || !/^\d+$/.test(aadhaarNumber)) {
        $('#aadhaarVerifiedMsg').hide();
        $('#aadharError').show().text("❌ Enter a valid 12-digit Aadhaar number.");
        $('.next').prop('disabled', true);
        return;
      }

      $.ajax({
        url: "{{ route('retailer.aadhaar.verify.ajax') }}",
        type: "POST",
        data: {
          aadhaar_number: aadhaarNumber,
          _token: "{{ csrf_token() }}"
        },
        success: function (res) {
          if (res.status === 'success') {
            $('#aadharError').hide();
            $('#aadhaarVerifiedMsg').show(); // ✅ Show success message
            $('.next').prop('disabled', false);
          } else {
            $('#aadhaarVerifiedMsg').hide();
            $('#aadharError').show().text("❌ " + res.message);
            $('.next').prop('disabled', true);
          }
        },
        error: function (xhr) {
          $('#aadhaarVerifiedMsg').hide();
          $('#aadharError').show().text("❌ Server Error: " + xhr.responseText);
          $('.next').prop('disabled', true);
        }
      });
    });
  });

</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const devicePriceInput = document.getElementById('device_price');
    const emiMonths = document.getElementById('emi_months');
    const downDropdown = document.getElementById('downpayment_dropdown');

    const downpaymentInfo = document.getElementById('downpayment_info');
    const minDownpaymentDisplay = document.getElementById('min_downpayment_display');
    const monthlyEmiDisplay = document.getElementById('monthly_emi_display');
    const resultBox = document.getElementById('emi_result');

    let forceTenPercent = false;

    devicePriceInput.addEventListener('input', updateDownpaymentOptions);
    emiMonths.addEventListener('change', calculateEMI);
    downDropdown.addEventListener('change', calculateEMI);

    function smartAlert(msg) {
      if (typeof Swal !== "undefined") {
        Swal.fire("Warning", msg, "warning");
      } else {
        alert(msg);
      }
    }

    function calculateMinimumDownpayment(price) {
      let dp = 0;
      if (price < 11500) {
        dp = price * 0.33;
      } else if (price <= 14800) {
        dp = price * 0.37;
      } else {
        dp = price * 0.40;
      }
      return Math.round(Math.round(dp / 500) * 500);
    }

    function updateDownpaymentOptions() {
      const price = parseInt(devicePriceInput.value) || 0;

      if (!price) {
        downDropdown.innerHTML = '<option value="">Select Downpayment</option>';
        downpaymentInfo.classList.add('d-none');
        resultBox.classList.add('d-none');
        return;
      }

      forceTenPercent = price >= 12500;

      const minDownpayment = calculateMinimumDownpayment(price);
      const maxDownpayment = price - 500;

      minDownpaymentDisplay.innerText = minDownpayment;
      downpaymentInfo.classList.remove('d-none');

      downDropdown.innerHTML = '<option value="">Select Downpayment</option>';
      for (let val = minDownpayment; val <= maxDownpayment; val += 500) {
        const option = document.createElement('option');
        option.value = val;
        option.textContent = `₹${val}`;
        downDropdown.appendChild(option);
      }

      downDropdown.value = minDownpayment;
      calculateEMI();
    }

    function calculateEMI() {
      const price = parseInt(devicePriceInput.value) || 0;
      const months = parseInt(emiMonths.value) || 0;
      const downpayment = parseInt(downDropdown.value) || 0;

      if (!price || !months || !downpayment) {
        resultBox.classList.add('d-none');
        return;
      }

      const loan = Math.round(price - downpayment);
      let rateMap = { 1: 15, 2: 12.2, 3: 11, 4: 11 };
      const minInterestMap = { 1: 290, 2: 690, 3: 1290, 4: 1790 };

      let rate = rateMap[months] || 0;

      if (forceTenPercent && (months === 3 || months === 4)) {
        rate = 10;
      }

      let interest = Math.round((loan * rate / 100) * months);
      if (interest < minInterestMap[months]) {
        interest = minInterestMap[months];
      }

      if (interest > 8000) {
        smartAlert("Might be your loan will not be approved. Try lower EMI Tenure.");
        resultBox.classList.add('d-none');
        document.getElementById('disburse_amount_input').value = "";
        document.getElementById('emi_input').value = "";
        document.getElementById('total_interest_input').value = "";
        document.getElementById('total_payment_input').value = "";
        monthlyEmiDisplay.innerText = "0";
        return;
      }

      const totalPayment = loan + interest;
      const monthlyEMI = Math.round(totalPayment / months);

      monthlyEmiDisplay.innerText = monthlyEMI;

      document.getElementById('disburse_amount_input').value = loan;
      document.getElementById('emi_input').value = monthlyEMI;
      document.getElementById('total_interest_input').value = interest;
      document.getElementById('total_payment_input').value = totalPayment;

      resultBox.classList.remove('d-none');
    }
  });
</script>


@if ($errors->any())
<div id="error-popup" class="error-popup">
  {{ $errors->first() }}
</div>

<script>
  setTimeout(function () {
    var popup = document.getElementById('error-popup');
    if (popup) {
      popup.remove();
    }
  }, 3000);
</script>
@endif


<script>

  $(document).ready(function () {
    $('#openModalBtn').on('click', function () {
      $('#errorModal').modal('show');
    });
    $('#errorModal .btn-close').on('click', function () {
      $('#errorModal').modal('hide');
    });
    setTimeout(function () {
      $('#errorModal').modal('hide');
    }, 4000);
  });


  $(document).ready(function () {
    $('#openModalBtn').on('click', function () {
      $('#aadhaarErrorModal').modal('show');
    });
    $('#aadhaarErrorModal .btn-close').on('click', function () {
      $('#aadhaarErrorModal').modal('hide');
    });
    setTimeout(function () {
      $('#aadhaarErrorModal').modal('hide');
    }, 4000);
  });


  $(document).ready(function () {
    // Initialize validation flags
    let isPincodeValid = false;
    //   let isAadhaarValid = false;
    // let isMobileValid = false;

    function updateNextButtonState() {
      // if (isPincodeValid && isMobileValid) {
      if (isPincodeValid) {
        $('.next').prop('disabled', false);
      } else {
        $('.next').prop('disabled', true);
      }
    }

    // Pincode validation
    $('#pincode').on('input', function () {
      const pincode = $(this).val().trim();
      if (pincode.length === 6) {
        $.ajax({
          url: '/retailer/check-pincode',
          method: 'GET',
          data: { pincode: pincode },
          success: function (response) {
            if (response.approved) {
              isPincodeValid = true;
              $('#pincodeError').hide();
            } else {
              isPincodeValid = false;
              Swal.fire({
                icon: 'error',
                title: 'Pincode Not Approved',
                text: 'Pincode is not approved or does not exist.',
                timer: 4000,
                timerProgressBar: true
              });

            }
            updateNextButtonState();
          },
          error: function () {
            isPincodeValid = false;
            Swal.fire({
              icon: 'error',
              title: 'Server Error',
              text: 'Server error occurred while checking the pincode.',
              timer: 4000,
              timerProgressBar: true
            });

            updateNextButtonState();
          }
        });
      } else {
        isPincodeValid = false;
        $('#pincodeError').show().text('Invalid Pincode.');
        updateNextButtonState();
      }
    });

    // Auto close pincode modal after 4 seconds
    $('#errorModal').on('shown.bs.modal', function () {
      setTimeout(function () {
        $('#errorModal').modal('hide');
      }, 4000);
    });


    // Aadhaar validation
    //     document.getElementById('aadhaar').addEventListener('input', function () {
    //      const aadharNumber = this.value;
    //      const errorElement = document.getElementById('aadharError');

    //     if (aadharNumber.length === 12 && /^\d+$/.test(aadharNumber)) {
    //       fetch('{{ route("retailer.check_aadhar") }}', {
    //         method: 'POST',
    //         headers: {
    //            'Content-Type': 'application/json',
    //           'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //          },
    //           body: JSON.stringify({ aadhaar_number: aadharNumber })
    //        })
    //        .then(response => response.json())
    //       .then(data => {
    //           if (data.exists) {
    //             isAadhaarValid = false;
    //           Swal.fire({
    //             icon: 'error',
    //            title: 'Aadhaar Exists',
    //               text: 'Aadhaar number already exists. Please check and try again.',
    //             timer: 5000,
    //              timerProgressBar: true
    //             });

    //          } else {
    //            isAadhaarValid = true;
    //              errorElement.style.display = 'none';
    //           }
    //     updateNextButtonState();
    //    })
    //   .catch(error => {
    //      console.error('Error:', error);
    //       isAadhaarValid = false;
    //        updateNextButtonState();
    //      });
    //     } else {
    //     isAadhaarValid = false;
    //     errorElement.style.display = 'block';
    //      errorElement.textContent = 'Invalid Aadhaar Number';
    //         updateNextButtonState();
    //      }
    //    });


    // Mobile validation
    // document.getElementById('mobileInput').addEventListener('input', function () {
    //     const mobile = this.value;
    //     const mobileError = document.getElementById('mobileError');

    //     if (/^[6-9]\d{9}$/.test(mobile)) {
    //       fetch(`/retailer/check-customer-mobile?mobile=${mobile}`)
    //       .then(response => {
    //         if (!response.ok) {
    //           throw new Error('Network response was not ok');
    //         }
    //         return response.json();
    //       })
    //       .then(data => {
    //         if (data.exists) {
    //           isMobileValid = true;
    //           Swal.fire({
    //             icon: 'error',
    //             title: 'Mobile Exists',
    //             text: 'This mobile number is already registered!',
    //             timer: 4000,
    //             timerProgressBar: true
    //           });

    //         } else {
    //           isMobileValid = true;
    //           mobileError.style.display = 'none';
    //         }
    //         updateNextButtonState();
    //       })
    //       .catch(error => {
    //         console.error('Fetch error:', error);
    //         isMobileValid = false;
    //         updateNextButtonState();
    //       });
    //     } else {
    //       isMobileValid = false;
    //       mobileError.innerText = "Invalid Mobile Number";
    //       mobileError.style.display = 'block';
    //       updateNextButtonState();
    //     }
    //   });

  });

  // $('.next').on('click', function () {
  //   let valid = true;
  //   const currentCard = $('#step-' + currentStep);

  //   currentCard.find('input, select').each(function () {
  //     if ($(this).prop('required') && !$(this).val()) {
  //       $(this).addClass('is-invalid');
  //       valid = false;
  //     } else {
  //       $(this).removeClass('is-invalid');
  //     }

  //     if ($(this).attr('type') === 'file' && $(this).prop('required') && this.files.length === 0) {
  //       $(this).addClass('is-invalid');
  //       valid = false;
  //     }
  //   });

  //   if (valid) {
  //     if (currentStep < 4) currentStep++;
  //     showStep(currentStep);
  //   }
  // });

  // $('.prev').on('click', function () {
  //   if (currentStep > 1) {
  //     currentStep--;
  //     showStep(currentStep);
  //   }
  // });

  // $('input, select').on('input change', function () {
  //   if ($(this).val()) {
  //     $(this).removeClass('is-invalid');
  //   }
  // });

  // showStep(currentStep);


  let currentStep = 0;
  let foundCustomerId = null;
  let foundCustomerData = null;

  function showStep(step) {
    $('.step').addClass('d-none');
    $('#step-' + step).removeClass('d-none');
  }

  function validateStep() {
    let valid = true;
    const currentCard = $('#step-' + currentStep);

    currentCard.find('input, select, textarea').each(function () {
      if ($(this).prop('required') && $(this).is(':visible')) {
        if ($(this).attr('type') === 'file') {
          if (this.files.length === 0) {
            $(this).addClass('is-invalid');
            valid = false;
          } else {
            $(this).removeClass('is-invalid');
          }
        } else if (!$(this).val()) {
          $(this).addClass('is-invalid');
          valid = false;
        } else {
          $(this).removeClass('is-invalid');
        }
      }
    });

    return valid;
  }

  // Step navigation buttons
  $('.next').on('click', function () {
    if (validateStep()) {
      if (currentStep < 3) {
        currentStep++;
        showStep(currentStep);
      }
    }
  });

  $('.prev').on('click', function () {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });

  // Remove validation error on input/change
  $(document).on('input change', 'input, select', function () {
    if ($(this).val()) {
      $(this).removeClass('is-invalid');
    }
  });

  // Initial step
  showStep(currentStep);

  // ------------------------------
  // Customer Search Logic
  // ------------------------------

  function searchCustomer(type) {
    let searchValue = '';

    if (type === 'aadhaar') {
      searchValue = $('#searchAadhaar').val().trim();
      if (searchValue.length !== 12) {
        Swal.fire('Invalid Aadhaar', 'Please enter a valid 12-digit Aadhaar number.', 'warning');
        return;
      }
    } else if (type === 'mobile') {
      searchValue = $('#searchMobile').val().trim();
      if (searchValue.length !== 10) {
        Swal.fire('Invalid Mobile', 'Please enter a valid 10-digit Mobile number.', 'warning');
        return;
      }
    } else {
      return;
    }

    Swal.showLoading();

    fetch(`/retailer/customer/search?type=${type}&value=${searchValue}`)
    .then(res => res.json())
    .then(data => {
      Swal.close();
      if (data.success) {
        $('#searchResults').show();
        $('#foundCustomerName').text(`${data.customer.customer_firstname} ${data.customer.customer_lastname}`);
        $('#foundCustomerMobile').text(data.customer.mobile);
        $('#foundCustomerLoans').text(data.active_loans);

        foundCustomerId = data.customer.id;
        foundCustomerData = data.customer;
      } else {
        Swal.fire('Not Found', data.message || 'Customer not found.', 'error');
        $('#searchResults').hide();
      }
    })
    .catch(() => {
      Swal.close();
      Swal.fire('Error', 'Something went wrong while searching.', 'error');
    });
  }

  // ------------------------------
  // Skip to New Customer
  // ------------------------------

  function skipToNewCustomer() {
    foundCustomerId = null;
    foundCustomerData = null;
    $('#isExistingCustomer').val(0);
    $('#existingCustomerBadge').hide();
    currentStep = 1;
    showStep(currentStep);
  }

  // ------------------------------
  // Use Existing Customer for Loan
  // ------------------------------

  function useExistingCustomer() {
    if (foundCustomerId && foundCustomerData) {
      $('#isExistingCustomer').val(1);
      $('#existingCustomerId').val(foundCustomerData.id);
      $('#existingCustomerBadge').show();

      $('#pincode').val(foundCustomerData.pincode);
      if (foundCustomerData.pincode) {
        $('.next').prop('disabled', false); // Enable button if pincode exists
      } else {
        $('.next').prop('disabled', true);  // Disable if no pincode
      }
      $('#aadhaar').val(foundCustomerData.aadhaar_number);
      $('#customer_firstname').val(foundCustomerData.customer_firstname);
      $('#customer_lastname').val(foundCustomerData.customer_lastname);
      $('#date_of_birth').val(foundCustomerData.date_of_birth);
      $('#father_name').val(foundCustomerData.father_name);
      $('#address1').val(foundCustomerData.address1);
      $('#address2').val(foundCustomerData.address2);
      $('#post').val(foundCustomerData.post);
      $('#village').val(foundCustomerData.village);
      $('#nearby').val(foundCustomerData.nearby);
      $('#mohalla').val(foundCustomerData.mohalla);
      $('#mobileInput').val(foundCustomerData.mobile);
      $('#alternate_mobile').val(foundCustomerData.alternate_mobile);

      $('#state_id').val(foundCustomerData.state_id).trigger('change');

      setTimeout(() => {
        $('#city_id').val(foundCustomerData.city_id).trigger('change');
      }, 800); 


      currentStep = 1;
      showStep(currentStep);

      setTimeout(() => {
        $('.next').eq(currentStep).trigger('click');
      }, 600); // Wait a bit to ensure city_id is populated
    } else {
      Swal.fire('Error', 'Please search and select a customer first.', 'warning');
    }
  }

  // ------------------------------
  // Go Back to Search
  // ------------------------------

  function goBackToSearch() {
    currentStep = 0;
    showStep(currentStep);
  }




  //     let currentStep = 1;

  // function showStep(step) {
  //   // Hide all steps
  //   $('.step').addClass('d-none');
  //   // Show the current step
  //   $('#step-' + step).removeClass('d-none');
  // }

  // $('.next').on('click', function () {
  //   let valid = true;
  //   const currentCard = $('#step-' + currentStep);

  //   // Validate all required inputs/selects inside current step
  //   currentCard.find('input, select').each(function () {
  //     if ($(this).prop('required')) {
  //       if ($(this).attr('type') === 'file') {
  //         if (this.files.length === 0) {
  //           $(this).addClass('is-invalid');
  //           valid = false;
  //         } else {
  //           $(this).removeClass('is-invalid');
  //         }
  //       } else if (!$(this).val()) {
  //         $(this).addClass('is-invalid');
  //         valid = false;
  //       } else {
  //         $(this).removeClass('is-invalid');
  //       }
  //     }
  //   });

  //   if (valid) {
  //     if (currentStep < 3) {
  //       currentStep++;
  //       showStep(currentStep);
  //     }
  //   }
  // });

  // $('.prev').on('click', function () {
  //   if (currentStep > 0) {
  //     currentStep--;
  //     showStep(currentStep);
  //   }
  // });

  // // Remove validation error on input or change
  // $('input, select').on('input change', function () {
  //   if ($(this).val()) {
  //     $(this).removeClass('is-invalid');
  //   }
  // });

  // // Function to skip to new customer form
  // function skipToNewCustomer() {
  //   currentStep = 1;
  //   $('#isExistingCustomer').val(0);
  //   $('#existingCustomerBadge').hide();
  //   $('#searchResults').hide();
  //   showStep(currentStep);
  // }

  // // Function to use existing customer and add loan
  // function useExistingCustomer() {
  //   currentStep = 1;
  //   $('#isExistingCustomer').val(1);
  //   $('#existingCustomerBadge').show();
  //   $('#searchResults').hide();
  //   showStep(currentStep);
  // }

  // // Initialize first step visible
  // showStep(currentStep);




  const today = new Date();
  const minDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
  const minDateString = minDate.toISOString().split('T')[0];
  document.getElementById('date_of_birth').setAttribute('max', minDateString);

  document.getElementById('date_of_birth').addEventListener('change', function () {
    const input = this.value;
    const dob = new Date(input);
    const today = new Date();

    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
      age--;
    }

    const nextButton = document.querySelector('.next');
    const dobError = document.getElementById('dobError');

    // Format to DD/MM/YYYY
    const formattedDOB = `${dob.getDate().toString().padStart(2, '0')}/${(dob.getMonth() + 1).toString().padStart(2, '0')
    }/${dob.getFullYear()}`;

    // Display formatted date
    document.getElementById('formattedDOB').innerText = `Selected DOB: ${formattedDOB}`;

    if (age < 18) {
      dobError.style.display = 'block';
      this.setCustomValidity('You must be at least 18 years old.');
      nextButton.disabled = true;

      // Show SweetAlert
      Swal.fire({
        icon: 'error',
        title: 'Age Restriction',
        text: 'You must be at least 18 years old.',
        confirmButtonColor: '#d33'
      });

    } else {
      dobError.style.display = 'none';
      this.setCustomValidity('');
      nextButton.disabled = false;
    }
  });

  let productPriceMap = {};

  $('#brand_id').on('change', function () {
    let brandId = $(this).val();
    $('#product_id').prop('disabled', true).html('<option value="">Loading...</option>');
    $('#product_price').val(''); // clear previous price

    if (brandId) {
      $.ajax({
        url: '/retailer/get-products/' + brandId,
        type: 'GET',
        success: function (products) {
          let options = '<option value="">Select Product</option>';
          productPriceMap = {}; // reset map

          $.each(products, function (key, product) {
            options += `<option value="${product.id}" data-price="${product.product_price}">${product.name}</option>`;
            productPriceMap[product.id] = product.product_price; // store in JS map
          });

          $('#product_id').html(options).prop('disabled', false);
        }
      });
    } else {
      $('#product_id').html('<option value="">Select Product</option>').prop('disabled', true);
    }
  });

  $('#product_id').on('change', function () {
    let selectedProductId = $(this).val();
    let selectedProductPrice = productPriceMap[selectedProductId] || '';

    $('#product_price').val(selectedProductPrice);
    // console.log("Selected Product ID:", selectedProductId);
    // console.log("Product Price:", selectedProductPrice);
  });


//   $(document).ready(function () {

//     // Init Select2 on city
//     // $('#city_id').select2({
//     //   placeholder: 'Select City',
//     //   width: '100%', // Full width on mobile
//     //   allowClear: false,
//     //   dropdownParent: $('#city_id').parent(),
//     //   theme: 'bootstrap-5'
//     // });

//     // Load initial cities if state pre-selected
//     const selectedState = $('#state_id').val();
//     if (selectedState) {
//       loadCities(selectedState);
//     }

//     // On state change
//     $('#state_id').on('change', function () {
//       const stateID = $(this).val();
//       loadCities(stateID);
//     });

//     function loadCities(stateID) {
//       // Disable city dropdown while loading
//       $('#city_id').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change');

//       if (stateID) {
//         $.ajax({
//           url: "/retailer/get-cities/" + stateID,
//           type: "GET",
//           dataType: "json",
//           success: function (res) {
//             let options = '<option value="">Select City</option>';
//             $.each(res, function (key, value) {
//               options += '<option value="' + value.id + '">' + value.name + '</option>';
//             });

//             // Update city dropdown
//             $('#city_id').html(options).prop('disabled', false).trigger('change');
//             if (foundCustomerData && foundCustomerData.city_id) {
//               $('#city_id').val(foundCustomerData.city_id).trigger('change');
//             }
//           },
//           error: function (xhr) {
//             console.error("City load failed:", xhr.responseText);
//             $('#city_id').html('<option value="">Failed to load cities</option>').prop('disabled', true).trigger('change');
//           }
//         });
//       } else {
//         // No state selected → reset city dropdown
//         $('#city_id').html('<option value="">Select City</option>').prop('disabled', true).trigger('change');
//       }
//     }

//   });


$(document).ready(function () {
  // Initialize Select2
  $('#city_id').select2({
    placeholder: "Select City",
    allowClear: true,
    width: '100%'
  });

  const selectedState = $('#state_id').val();
  if (selectedState) {
    loadCities(selectedState);
  }

  $('#state_id').on('change', function () {
    const stateID = $(this).val();
    loadCities(stateID);
  });

  function loadCities(stateID) {
    $('#city_id').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change');

    if (stateID) {
      $.ajax({
        url: "/retailer/get-cities/" + stateID,
        type: "GET",
        dataType: "json",
        success: function (res) {
          let options = '<option value="">Select City</option>';
          $.each(res, function (key, value) {
            options += '<option value="' + value.id + '">' + value.name + '</option>';
          });

          $('#city_id').html(options).prop('disabled', false).trigger('change');

          if (typeof foundCustomerData !== 'undefined' && foundCustomerData.city_id) {
            $('#city_id').val(foundCustomerData.city_id).trigger('change');
          }
        },
        error: function () {
          $('#city_id').html('<option value="">Failed to load cities</option>').prop('disabled', true).trigger('change');
        }
      });
    } else {
      $('#city_id').html('<option value="">Select City</option>').prop('disabled', true).trigger('change');
    }
  }
});



  document.addEventListener('DOMContentLoaded', function () {
    const products = @json($products);

    let selectedProductPrice = 0;

    const brandSelect = document.getElementById('brand_id');
    const productSelect = document.getElementById('product_id');
    const productPriceInput = document.getElementById('product_price');
    const devicePriceInput = document.getElementById('device_price');

    if (!brandSelect || !productSelect || !productPriceInput || !devicePriceInput) {
      console.error("One or more required elements are missing from the DOM.");
      return;
    }

    brandSelect.addEventListener('change', function () {
      const brandId = this.value;
      // console.log("Selected Brand ID:", brandId);

      productSelect.innerHTML = `<option value="">Select Product</option>`;
      productSelect.disabled = false;
      selectedProductPrice = 0;
      productPriceInput.value = '';

      products.forEach(product => {
        if (product.brand_id == brandId) {
          const option = document.createElement('option');
          option.value = product.id;
          option.text = `${product.product_name} (₹${product.product_price})`;
          option.setAttribute('data-price', product.product_price);
          productSelect.appendChild(option);
        }
      });
    });

    productSelect.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      selectedProductPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;

      productPriceInput.value = selectedProductPrice > 0 ? selectedProductPrice : '';
      // console.log("Selected Product ID:", this.value);
      // console.log("Product Price:", selectedProductPrice);
    });

    devicePriceInput.addEventListener('input', function () {
      const disburseAmount = parseFloat(this.value);
      // console.log("Entered Disbursement Amount:", disburseAmount);

      if (selectedProductPrice > 0 && disburseAmount > 0) {
        if (disburseAmount > selectedProductPrice) {
          // console.log("Disbursement is GREATER than Product Price — Showing Error");

          Swal.fire({
            icon: 'error',
            title: 'Invalid Disbursement Amount',
            text: `Disbursement amount (₹${disburseAmount}) cannot be more than Product Price (₹${selectedProductPrice}).`,
          });

          this.value = '';
          this.focus();
        } else {
          // console.log("Disbursement is VALID");
        }
      } else {
        //  console.log("Waiting for valid product selection and disbursement input...");
      }
    });
  });


  //   $('#state_id').on('change', function () {
  //    let stateID = $(this).val();
  //    $('#city_id').prop('disabled', true).html('<option value="">Loading...</option>');

  //    if (stateID) {
  //      $.ajax({
  //        url: "/retailer/get-cities/" + stateID,

  //        type: "GET",
  //        success: function (res) {
  //           let options = '<option value="">Select City</option>';
  //           $.each(res, function (key, value) {
  //             options += '<option value="' + value.id + '">' + value.name + '</option>';
  //           });
  //        $('#city_id').html(options).prop('disabled', false);
  //        },
  //          error: function (xhr) {
  //          console.error("City load failed:", xhr.responseText);
  //            $('#city_id').html('<option value="">Failed to load cities</option>').prop('disabled', true);
  //          }
  //        });
  //     }
  //    });

  function previewImage(event, previewId) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById(previewId).src = e.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      document.getElementById(previewId).src = 'https://cdn-icons-png.freepik.com/512/6870/6870041.png';
    }
  }


  function validateMobileNumbers() {
    const altMobile = document.getElementById("alternate_mobile").value;
    const altMobileError = document.getElementById("altMobileError");

    if (altMobile.length === 10 && /^[6-9][0-9]{9}$/.test(altMobile)) {
      altMobileError.style.display = "none";
    } else if (altMobile.length === 10) {
      altMobileError.innerText = "Invalid Mobile Number.";
      altMobileError.style.display = "block";
    } else {
      altMobileError.style.display = "none"; // Hide while typing until 10 digits
    }
  }

</script>

@endsection