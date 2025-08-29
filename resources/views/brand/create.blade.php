@extends('layouts.layout')

@section('content')

<body class="theme-1">
  <form method="POST" action="{{ route('admin.brandStore') }}" enctype="multipart/form-data">
    @csrf
    <div class="page-content-wrapper">
      <div class="content-container">
        <div class="page-content">
          <div class="content-header">
            <h1>Add Brand</h1>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item">Brand</li>
              <li class="breadcrumb-item">Add Brand</li>
            </ul>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="row">
                <div class="col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="mb-3">Brand Image</h4>
                      <div class="position-relative overflow-hidden rounded">
                        <img id="preview-image" src="https://cdn-icons-png.freepik.com/512/6870/6870041.png"
                             alt="image" class="w-100 mb-3"
                             style="max-height: 200px; object-fit: contain;">
                      </div>

                      <!-- Upload Input -->
                      <input type="file" id="upload-image" name="brand_image" class="form-control mt-3"
                             accept="image/*">
                    </div>
                  </div>
                </div>
                <div class="col-lg-8">
                  <div class="card">
                    <div class="card-header">
                      <h4>Brand Information</h4>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" name="brand_name" class="form-control"
                                   placeholder="Enter Brand name" required>
                          </div>
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
</body>

<script>
  document.getElementById('upload-image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview-image');

    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      preview.src = 'https://cdn-icons-png.freepik.com/512/6870/6870041.png'; // default fallback
    }
  });
</script>

@endsection