@extends('layouts.layout')

@section('content')
<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Add Retailer</h1>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Retailer</li>
            <li class="breadcrumb-item">Add Retailer</li>
          </ul>
        </div>

        <form action="{{ route('admin.retailerStore') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row justify-content-center p-4">
            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="flex-grow-1">All Retailer</h5>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label">First Name<span class="text-danger">*</span></label>
                          <input type="text" name="firstname" class="form-control" placeholder="Enter Firstname" required>
                        </div>
                        
                        <div class="col-md-6">
                          <label class="form-label">Last Name<span class="text-danger">*</span></label>
                          <input type="text" name="lastname" class="form-control" placeholder="Enter Lastname" required>
                        </div>
                        
                        <div class="col-md-12">
                          <label class="form-label">Shop Name<span class="text-danger">*</span></label>
                          <input type="text" name="shop_name" class="form-control" placeholder="Enter Shop Name" required>
                        </div>
                        <div class="col-md-12">
                          <label class="form-label">Address Line 1<span class="text-danger">*</span></label>
                          <input type="text" name="address1" class="form-control" placeholder="Enter Address" required>
                        </div>
                        <div class="col-md-12">
                          <label class="form-label">Address Line 2</label>
                          <input type="text" name="address2" class="form-control" placeholder="Enter Address">
                        </div>
                        <div class="col-md-6">
                          <label>State<span class="text-danger">*</span></label>
                          <select name="state_id" id="state_id" class="form-select" required>
                              <option value="">Select State</option>
                              @foreach ($states as $state)
                                  <option value="{{ $state->id }}">{{ $state->name }}</option>
                              @endforeach
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label>City<span class="text-danger">*</span></label>
                          <select name="city_id" id="city_id" class="form-select" required disabled>
                              <option value="">Select City</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Zipcode<span class="text-danger">*</span></label>
                          <input type="number" name="zipcode" class="form-control" maxlength="6" placeholder="Enter Pincode"
                            oninput="this.value=this.value.slice(0,6)" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Mobile<span class="text-danger">*</span></label>
                          <input type="number" name="mobile_no" class="form-control" maxlength="10"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" placeholder="Enter Mobile Number"
                            required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Email<span class="text-danger">*</span></label>
                          <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Password<span class="text-danger">*</span></label>
                          <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                        </div>
                      </div>
                      <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary ms-3 px-4">Reset</button>
                      </div>
                    </div>
                  </div>
                </div>          
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#state_id').on('change', function () {
    console.log("call the function")
    let stateID = $(this).val();
    console.log("Selected stateID:", stateID);

    $('#city_id').prop('disabled', true).html('<option value="">Loading...</option>');

    if (stateID) {
      $.ajax({
        url: "/admin/get-cities/" + stateID,
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

@endsection