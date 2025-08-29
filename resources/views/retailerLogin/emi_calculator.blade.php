@extends('retailerLogin.layout.layout')

@section('content')
<body class="theme-1">
  <!-- EMI Calculator - Stylish UI -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(to right, #667eea, #764ba2);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }

    .card {
      border: none;
      border-radius: 1rem;
      background: #fff;
    }

    h4 {
      font-weight: 600;
      color: #4b4b4b;
    }

    label {
      font-weight: 500;
      color: #333;
    }

    .form-control,
    .form-select {
      border-radius: 0.75rem;
      box-shadow: none;
      transition: all 0.3s ease-in-out;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #764ba2;
      box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
    }

    .result p {
      font-size: 1rem;
      margin-bottom: 0.5rem;
      color: #333;
    }

    .result strong {
      color: #764ba2;
    }

    .shadow-sm {
      box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }

    .btn-purple {
      background-color: #764ba2;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 0.75rem;
      transition: background 0.3s;
    }

    .btn-purple:hover {
      background-color: #5a3d8e;
    }
  </style>

  <div class="container py-4" style="margin-top: 45px;">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-10">
        <div class="card p-4 shadow-sm">
          <form id="emi_form">
            <h4 class="text-center mb-4">📱 EMI Calculator</h4>

            <!-- Device Price -->
            <div class="mb-3">
              <label class="form-label">Disbursement Amount</label>
              <input type="number" id="device_price" class="form-control" placeholder="Enter device price">
            </div>

            <!-- Minimum Downpayment Display -->
            <div class="result d-none" id="downpayment_info">
              <p><strong>Min. Down-Payment:</strong> ₹<span id="min_downpayment_display">0</span></p>
            </div>

            <!-- Downpayment Dropdown -->
            <div class="mb-3">
              <label class="form-label">Select Down-Payment</label>
              <select id="downpayment_dropdown" class="form-select">
                <option value="">Select Downpayment</option>
              </select>
            </div>

            <!-- EMI Duration -->
            <div class="mb-3">
              <label class="form-label">EMI Duration</label>
              <select id="emi_months" class="form-select">
                <option value="" selected disabled>Select EMI Duration</option>
                <option value="1">1 Month</option>
                <option value="2">2 Months</option>
                <option value="3">3 Months</option>
                <option value="4">4 Months</option>
              </select>
            </div>

            <!-- EMI Results -->
            <hr>

            <div class="result d-none" id="emi_result">
              <p class="d-none"><strong>Downpayment:</strong> ₹<span id="selected_downpayment_display">0</span></p>
              <p class="d-none"><strong>Loan Amount:</strong> ₹<span id="loan_amount_display">0</span></p>
              <p><strong>Monthly EMI:</strong> ₹<span id="monthly_emi_display">0</span></p>
              <p class="d-none"><strong>Total Interest:</strong> ₹<span id="total_interest_display">0</span></p>
              <p class="d-none"><strong>Total Payment:</strong> ₹<span id="total_payment_display">0</span></p>
            </div>

            <!-- Optional Submit/Calculate Button 
<div class="text-center mt-3">
<button class="btn btn-purple" id="calculate_btn">Calculate EMI</button>
</div> -->

            <div class="text-end">
              <a href="{{ route('retailer.emi.form') }}" class="btn btn-outline-secondary ms-2">Reset</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const devicePriceInput = document.getElementById('device_price');
      const emiMonths = document.getElementById('emi_months');
      const downDropdown = document.getElementById('downpayment_dropdown');

      const downpaymentInfo = document.getElementById('downpayment_info');
      const minDownpaymentDisplay = document.getElementById('min_downpayment_display');
      const selectedDownpaymentDisplay = document.getElementById('selected_downpayment_display');

      const loanAmountDisplay = document.getElementById('loan_amount_display');
      const monthlyEmiDisplay = document.getElementById('monthly_emi_display');
      const totalInterestDisplay = document.getElementById('total_interest_display');
      const totalPaymentDisplay = document.getElementById('total_payment_display');
      const resultBox = document.getElementById('emi_result');

      let enforcingMin50 = false;

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
          dp = Math.round(dp / 500) * 500;
        } else if (price <= 14800) {
          dp = price * 0.37;
          dp = Math.round(dp / 500) * 500;
        } else {
          dp = price * 0.40;
          dp = Math.round(dp / 500) * 500;
        }

        return Math.round(dp);
      }

      let forceTenPercent = false;

      function updateDownpaymentOptions() {
        const price = parseInt(devicePriceInput.value) || 0;

        if (!price) {
          downDropdown.innerHTML = '<option value="">Select Downpayment</option>';
          downpaymentInfo.classList.add('d-none');
          return;
        }

        // Set the forceTenPercent flag
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
        let interest;
        let customRateApplied = false;

        // Apply 10% interest if price >= 12500 and months = 3 or 4
        if (forceTenPercent && (months === 3 || months === 4)) {
          rate = 10;
          interest = Math.round((loan * rate / 100) * months);
          customRateApplied = true;
        } else {
          interest = Math.round((loan * rate / 100) * months);
        }

        if (interest < minInterestMap[months]) {
          interest = minInterestMap[months];
        }


        if (interest > 8000) {
          smartAlert("Might be your loan will not be approved. Try lower EMI Tenure.");
          resultBox.classList.add('d-none');
          [selectedDownpaymentDisplay, loanAmountDisplay, monthlyEmiDisplay,
           totalInterestDisplay, totalPaymentDisplay].forEach(el => el.innerText = "0");
          return;
        }

        const totalPayment = loan + interest;
        const monthlyEMI = Math.round(totalPayment / months);

        selectedDownpaymentDisplay.innerText = downpayment;
        loanAmountDisplay.innerText = loan;
        totalInterestDisplay.innerText = interest;
        totalPaymentDisplay.innerText = totalPayment;
        monthlyEmiDisplay.innerText = monthlyEMI;

        resultBox.classList.remove('d-none');

        // Update dropdown labels
        const updateOptionLabel = (value, text) => {
          const option = Array.from(emiMonths.options).find(opt => opt.value === value);
          if (option) option.text = text;
        };

        updateOptionLabel("1", "1 Month");
        updateOptionLabel("2", "2 Months");

        if (customRateApplied) {
          if (months === 3) updateOptionLabel("3", "3 Months");
          if (months === 4) updateOptionLabel("4", "4 Months");
        } else {
          updateOptionLabel("3", "3 Months");
          updateOptionLabel("4", "4 Months");
        }
      }

    });
  </script>

</body>
@endsection
