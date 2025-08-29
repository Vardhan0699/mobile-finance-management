@extends('layouts.layout')

@section('content')

<body class="theme-1">
  <form method="POST" action="{{ route('admin.productStore') }}" enctype="multipart/form-data">
    @csrf
    <div class="page-content-wrapper">
      <div class="content-container">
        <div class="page-content">
          <div class="content-header">
            <h1>Add Product</h1>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item">Product</li>
              <li class="breadcrumb-item">Add Product</li>
            </ul>
          </div>



          <div class="row">
            <div class="col-12">
              <div class="row">

                <div class="col-lg-10">
                  <div class="card">
                    <div class="card-header">
                      <h4>Product Informations</h4>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="form-label">Brand</label>
                            <select class="form-select" name="brand_id" required>
                              <option value="">Select Brand</option>
                              @foreach($brands as $brand)
                              <option value="{{ $brand->id }}">{{ $brand->brand_name }}
                              </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control"
                                   placeholder="Enter Product name" required>
                          </div>
                        </div>
                      </div>

                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="form-label">Product Price</label>
                          <input type="text" name="product_price" class="form-control"
                                 placeholder="Enter Product Price" required pattern="\d+"
                                 oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                      </div>


                      <div class="text-end">
                        <button class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary ms-2">Reset</button>
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

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @if(session('success'))
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: '{{ session('success') }}',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'OK'
    });
  </script>
  @endif


</body>

@endsection