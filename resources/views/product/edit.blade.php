@extends('layouts.layout')

@section('content')

<body class="theme-1">
  <form method="POST" action="{{ route('admin.productUpdate', $product->id) }}"
        enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="page-content-wrapper">
      <div class="content-container">
        <div class="page-content">
          <div class="content-header">
            <h1>Update Product</h1>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item">Product</li>
              <li class="breadcrumb-item">Product List</li>
              <li class="breadcrumb-item">Update Product</li>
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
                              <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                {{ $brand->brand_name }}
                                <!-- Displaying the brand name here -->
                              </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control"
                                   value="{{ old('product_name', $product->product_name) }}"
                                   required>
                          </div>
                        </div>
                      </div>

                      <div class="col-sm-6">
                        <div class="form-group">
                          <label class="form-label">Product Price</label>
                          <input type="text" name="product_price" class="form-control"
                                 value="{{ old('product_price', $product->product_price) }}" required pattern="\d+"
                                 oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                      </div>

                      <div class="text-end">
                        <button class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary ms-2">reset</button>
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