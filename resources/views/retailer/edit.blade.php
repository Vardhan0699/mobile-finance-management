@extends('layouts.layout')

@section('content')

<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Edit Retailer</h1>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Retailer</li>
            <li class="breadcrumb-item">Edit Retailer</li>
          </ul>
        </div>

        <form action="{{ route('admin.retailerUpdate', $retailer->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="row" style="padding-top: 25px;">
            <div class="col-11">
              <div class="row">
                <div class="col-11">
                  <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="flex-grow-1">Edit Retailer</h5>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label">First Name</label>
                          <input type="text" name="firstname" class="form-control" value="{{ old('firstname', $retailer->firstname) }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Last Name</label>
                          <input type="text" name="lastname" class="form-control" value="{{ old('lastname', $retailer->lastname) }}" required>
                        </div>
                        <div class="col-md-12">
                          <label class="form-label">Shop Name</label>
                          <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name', $retailer->shop_name) }}" required>
                        </div>
                        <div class="col-md-12">
                          <label class="form-label">Address Line 1</label>
                          <input type="text" name="address1" class="form-control" value="{{ old('address1', $retailer->address1) }}" required>
                        </div>
                        <div class="col-md-12">
                          <label class="form-label">Address Line 2</label>
                          <input type="text" name="address2" class="form-control" value="{{ old('address2', $retailer->address2) }}">
                        </div>
                        <div class="col-md-6">
                          <label>State</label>
                          <select name="state_id" id="state_id" class="form-select" required>
                              <option value="">Select State</option>
                               @foreach ($states as $state)
                                  <option value="{{ $state->id }}" {{ old('state_id', $retailer->state_id) == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                  </option>
                              @endforeach
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label>City</label>
                          <select name="city_id" id="city_id" class="form-select" required disabled>
                              <option value="">Select City</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">ZIPCODE</label>
                          <input type="number" name="zipcode" class="form-control" value="{{ old('zipcode', $retailer->zipcode) }}" maxlength="6" 
                            oninput="this.value=this.value.slice(0,6)" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Mobile</label>
                          <input type="number" name="mobile_no" class="form-control" maxlength="10" value="{{ old('mobile_no', $retailer->mobile_no) }}"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" 
                            required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Email</label>
                          <input type="email" name="email" class="form-control" value="{{ old('email', $retailer->email) }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Password</label>
                          <input type="password" name="password" class="form-control" required>
                        </div>
                      </div>
                      <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary ms-2">Reset</button>
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
  $(document).ready(function () {
    var stateID = $('#state_id').val();
    if (stateID) {
      loadCities(stateID);
    }

    $('#state_id').on('change', function () {
      let stateID = $(this).val();
      loadCities(stateID);
    });

    function loadCities(stateID) {
      $('#city_id').prop('disabled', true).html('<option value="">Loading...</option>');
      if (stateID) {
        $.ajax({
          url: "/admin/get-cities/" + stateID,
          type: "GET",
          success: function (res) {
            let options = '<option value="">Select City</option>';
            $.each(res, function (key, value) {
              options += '<option value="' + value.id + '" ' + (value.id == '{{ old('city_id', $retailer->city_id) }}' ? 'selected' : '') + '>' + value.name + '</option>';
            });
            $('#city_id').html(options).prop('disabled', false);
          },
          error: function (xhr) {
            console.error("City load failed:", xhr.responseText);
            $('#city_id').html('<option value="">Failed to load cities</option>').prop('disabled', true);
          }
        });
      }
    }
  });
</script>


@endsection