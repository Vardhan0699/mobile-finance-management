@extends('layouts.layout')

@section('content')

<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Admin Profile</h1>
        </div>
        
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

        <form action="{{ route ('admin.profileUpdate') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row" style="padding-top: 25px;">
            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="flex-grow-1">Admin Details</h5>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label">First Name</label>
                          <input type="text" name="firstname" class="form-control" value="{{ old('firstname', $admin->firstname) }}" placeholder="Enter Firstname" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Last Name</label>
                          <input type="text" name="lastname" class="form-control" value="{{ old('lastname', $admin->lastname) }}" placeholder="Enter Lastname" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Mobile</label>
                          <input type="number" name="mobile_no" class="form-control" maxlength="10"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" value="{{ old('mobile_no', $admin->mobile_no) }}" placeholder="Enter Mobile Number"
                            required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Email</label>
                          <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" placeholder="Enter Email" disabled required>
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

@endsection