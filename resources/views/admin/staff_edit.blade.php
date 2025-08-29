@extends('layouts.layout')

@section('content')
<div class="page-content-wrapper theme-1">
  <div class="content-container">
    <div class="page-content">
      <div class="content-header">
        <h1>Update Staff</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item">Staff</li>
          <li class="breadcrumb-item">Update Staff</li>
        </ul>
      </div>

      <div class="container py-4">
        <div class="row justify-content-center">
          <div class="col-md-10 col-lg-12">
            <div class="card rounded-2 border-0">
              <div class="card-header rounded-top-4 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Update Staff</h4>
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm float-end">
                  <i class="fa fa-arrow-left me-1"></i> Back
                </a>

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
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.update', $admin->id) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">First Name <span class="text-danger">*</span></label>
                      <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror"
                             value="{{ old('firstname', $admin->firstname) }}" placeholder="Enter first name" required
                             style="border: 1px solid #ced4da;">
                      @error('firstname')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Last Name <span class="text-danger">*</span></label>
                      <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror"
                             value="{{ old('lastname', $admin->lastname) }}" placeholder="Enter last name" required
                             style="border: 1px solid #ced4da;">
                      @error('lastname')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                      <input type="text" name="mobile_no" class="form-control @error('mobile_no') is-invalid @enderror"
                             value="{{ old('mobile_no', $admin->mobile_no) }}" maxlength="10"
                             pattern="^[6-9][0-9]{9}$" inputmode="numeric" placeholder="Enter mobile number" required
                             oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                             style="border: 1px solid #ced4da;">
                      @error('mobile_no')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Email <span class="text-danger">*</span></label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                             value="{{ old('email', $admin->email) }}" placeholder="Enter email address" required
                             style="border: 1px solid #ced4da;">
                      @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Password</label>
                      <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                             placeholder="Enter new password (leave blank to keep old)" style="border: 1px solid #ced4da;">
                      @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Confirm Password</label>
                      <input type="password" name="password_confirmation" class="form-control"
                             placeholder="Confirm new password" style="border: 1px solid #ced4da;">
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">Role <span class="text-danger">*</span></label>
                      <select name="role_id" id="roleSelect" class="form-control" required style="border: 1px solid #ced4da;">
                        <option value="" disabled>Select Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                                {{ $admin->role_id == $role->id ? 'selected' : '' }}
                          {{ $role->id == 1 ? 'disabled' : '' }}>
                          {{ ucfirst($role->name) }}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-4" id="pincodeSection" style="display: none;" >
                      <label class="form-label">Pincode(s)</label>
                      <select name="zipcode[]" class="form-control" multiple style="border: 1px solid #ced4da;">
                        @php $selectedZipcodes = json_decode($admin->zipcode ?? '[]', true); @endphp
                        @foreach($pincodes as $pincode)
                        <option value="{{ $pincode->id }}"
                                {{ in_array($pincode->id, $selectedZipcodes ?? []) ? 'selected' : '' }}>
                          {{ $pincode->pincode }}
                        </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Update</button>
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
    const selectedRole = roleSelect.options[roleSelect.selectedIndex]?.text.toLowerCase();

    // Show on load if recovery
    if (selectedRole === 'recovery') {
    pincodeSection.style.display = 'block';
    }

    // Change event
    roleSelect.addEventListener('change', function () {
    const selectedRoleText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
    pincodeSection.style.display = selectedRoleText === 'recovery' ? 'block' : 'none';
  });
  });
</script>
@endsection
