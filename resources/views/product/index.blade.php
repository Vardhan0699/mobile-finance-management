@extends('layouts.layout')

@section('content')

@php
$adminUser = auth()->guard('admin')->user();
$canViewProduct = $adminUser && $adminUser->hasPermission('product', 'read');
$canEditProduct = $adminUser && $adminUser->hasPermission('product', 'update');
$canDeleteProduct = $adminUser && $adminUser->hasPermission('product', 'delete');
@endphp


<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Product List</h1>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Product</li>
            <li class="breadcrumb-item">Product List</li>
          </ul>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12">
                <div class="card table-card">
                  <div
                       class="card-header card-header d-flex align-items-center justify-content-between">
                    <h5 class="flex-grow-1">All Product</h5>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table" id="pc-dt-export">
                        <thead>
                          <tr>
                            <th class="text-center">ID</th>

                            <th class="text-center">Brand Name</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Product Image</th>
                            @if($canEditProduct || $canDeleteProduct)
                            <th class="text-end">Status</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($products as $product)
                          <tr>
                            <td class="text-center">

                              <h6 class="mb-0">{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</h6>

                            </td>

                            <td class="text-center">{{ $product->brand->brand_name ?? 'N/A' }}</td>
                            <td class="text-center">{{ $product->product_name ?? 'N/A' }}</td>
                            <td class="text-center">₹{{ $product->product_price ?? 'N/A' }}</td>
                            @if($canEditProduct || $canDeleteProduct)
                            <td class="text-end">
                              @if($canEditProduct)
                              <a href="{{ route('admin.productEdit', $product->id) }}"
                                 class="btn btn-icon btn-md" data-bs-toggle="tooltip"
                                 title="Edit">
                                <i class="ti ti-pencil"></i>
                              </a>
                              @endif

                              @if($canDeleteProduct)
                              <form action="{{ route('admin.productDestroy', $product->id) }}"
                                    method="POST"
                                    class="delete-product-form"
                                    data-id="{{ $product->id }}"
                                    style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-icon btn-md text-danger"
                                        data-bs-toggle="tooltip"
                                        title="Delete">
                                  <i class="ti ti-archive"></i>
                                </button>
                              </form>
                              @endif
                            </td>
                            @endif
                          </tr>
                          @empty
                          <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No
                              Product found.</td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pb-1 px-3">
                      {{ $products->links('pagination::simple-bootstrap-5') }}
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
</body>

<!-- Toastr JS & CSS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

<!-- Add in your layout head or just before </body> -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-product-form');

    deleteForms.forEach(form => {
      form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting immediately

        Swal.fire({
          title: 'Are you sure?',
          text: "This action cannot be undone!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit(); // Submit the form after confirmation
          }
        });
      });
    });
  });
</script>

@endsection