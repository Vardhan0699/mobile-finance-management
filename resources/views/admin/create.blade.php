@extends('layouts.layout')

@section('content')
<div class="page-content-wrapper theme-1">
  <div class="content-container">
    <div class="page-content">
      <div class="content-header">
        <h1>Add Staff</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item">Staff</li>
          <li class="breadcrumb-item">Add Staff</li>
        </ul>
      </div>

      <div class="container py-4">
        <div class="row justify-content-center">
          <div class="col-md-10 col-lg-12">
            <div class="card rounded-2 border-0">
              <div class="card-header rounded-top-4">
                <h4 class="mb-0">Create New Staff</h4>
              </div>
              <div class="card-body p-4">

                @if ($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success">
                  {{ session('success') }}
                </div>
                @endif
                <form action="{{ route('admin.register') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row g-3">

                    <div class="col-md-6">
                      <label class="form-label">First Name <span class="text-danger">*</span></label>
                      <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror"
                             placeholder="Enter first name" required style="border: 1px solid #ced4da;">
                      @error('firstname')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Last Name <span class="text-danger">*</span></label>
                      <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror"
                             placeholder="Enter last name" required style="border: 1px solid #ced4da;">
                      @error('lastname')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                      <input type="text" name="mobile_no" class="form-control @error('mobile_no') is-invalid @enderror"
                             maxlength="10" pattern="^[6-9][0-9]{9}$" inputmode="numeric" placeholder="Enter mobile number"
                             required
                             oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10); this.setCustomValidity('')"
                             oninvalid="this.setCustomValidity('Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9')"
                             style="border: 1px solid #ced4da;">
                      @error('mobile_no')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Email <span class="text-danger">*</span></label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                             placeholder="Enter email address" required style="border: 1px solid #ced4da;">
                      @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Password <span class="text-danger">*</span></label>
                      <input type="password" name="password"
                             class="form-control @error('password') is-invalid @enderror" placeholder="Create password"
                             required style="border: 1px solid #ced4da;">
                      @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                      <input type="password" name="password_confirmation" class="form-control"
                             placeholder="Confirm password" required style="border: 1px solid #ced4da;">
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">Role <span class="text-danger">*</span></label>
                      <select name="role_id" id="roleSelect" class="form-control" required style="border: 1px solid #ced4da;">
                        <option value="" disabled selected>Select Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $role->id == 1 ? 'disabled' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-4" id="pincodeSection" style="display: none;"> <!-- initially hidden -->
                      <label class="form-label">Pincode(s) <span class="text-danger">*</span></label>
                      <select name="zipcode[]" class="form-control @error('pincode_id') is-invalid @enderror" multiple style="border: 1px solid #ced4da;">
                        @foreach($pincodes as $pincode)
                        <option value="{{ $pincode->id }}">{{ $pincode->pincode }}</option>
                        @endforeach
                      </select>
                      @error('pincode_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                  </div>

                  <div class="d-flex justify-content-end" style="margin-top: 18px;">
                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary ms-3 px-4">Reset</button>
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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('roleSelect');
    const pincodeSection = document.getElementById('pincodeSection');

    roleSelect.addEventListener('change', function () {
      const selectedRoleText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

      if (selectedRoleText === 'recovery') {
        pincodeSection.style.display = 'block';
      } else {
        pincodeSection.style.display = 'none';
      }
    });
  });
</script>

@endsection