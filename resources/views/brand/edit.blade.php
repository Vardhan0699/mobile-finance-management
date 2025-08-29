@extends('layouts.layout')

@section('content')

    <body class="theme-1">
        <div class="page-content-wrapper">
            <div class="content-container">
                <div class="page-content">
                    <div class="content-header">
                        <h1>Update Brand</h1>
                    </div>
                    <form method="POST" action="{{ route('admin.brandUpdate', $brand->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="mb-3">Brand Image</h4>
                                                <div class="position-relative overflow-hidden rounded">
                                                   <img id="preview-image" src="{{ $presignedUrl ?? asset('images/no-image.png') }}"
             class="img-fluid rounded" style="max-height: 200px; contain;">
                                                </div>
                                                <!-- Upload Input -->
                                                <input type="file" id="upload-image" name="brand_image"
                                                    class="form-control mt-3" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Brand Informations</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Name</label>
                                                            <input type="text" name="brand_name" class="form-control"
                                                                placeholder="Enter Brand name"
                                                                value="{{ old('brand_name', $brand->brand_name) }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button class="btn btn-primary">Submit</button>
                                                    <button class="btn btn-outline-secondary ms-2">reset</button>
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

    <script>
       // document.getElementById('upload-image').addEventListener('change', function (event) {
        //    const file = event.target.files[0];
        //    const preview = document.getElementById('preview-image');

        //    if (file && file.type.startsWith('image/')) {
        //        const reader = new FileReader();
        //        reader.onload = function (e) {
          //          preview.src = e.target.result;
         //       };
         //       reader.readAsDataURL(file);
        //    } else {
        //        preview.src = '{{ $brand->brand_image ?? 'https://cdn-icons-png.freepik.com/512/6870/6870041.png' }}';
       //     }
      //   });

      document.getElementById('upload-image').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview-image').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
    </script>

@endsection