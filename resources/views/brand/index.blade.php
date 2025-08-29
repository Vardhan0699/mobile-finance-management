@extends('layouts.layout')

@section('content')

@php
$adminUser = auth()->guard('admin')->user();
$canViewBrand = $adminUser && $adminUser->hasPermission('brand', 'read');
$canEditBrand = $adminUser && $adminUser->hasPermission('brand', 'update');
$canDeleteBrand = $adminUser && $adminUser->hasPermission('brand', 'delete');
@endphp


<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Brand List</h1>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Brand</li>
            <li class="breadcrumb-item">Brand List</li>
          </ul>
        </div>

        @if (session('success'))
        <script>
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
          });
        </script>
        @endif

        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12">
                <div class="card table-card">
                  <div
                       class="card-header card-header d-flex align-items-center justify-content-between">
                    <h5 class="flex-grow-1">All Brands</h5>
                    <!-- <div class="btn-group">
<button class="btn btn-light-primary btn-sm csv">Export CSV</button>
<button class="btn btn-light-primary btn-sm sql">Export SQL</button>
<button class="btn btn-light-primary btn-sm txt">Export TXT</button>
<button class="btn btn-light-primary btn-sm json">Export JSON</button>
</div> -->
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table" id="pc-dt-export">
                        <thead>
                          <tr>
                            <th class="text-center">ID</th>

                            <th class="text-center">Brand Name</th>
                            <th class="text-center">Brand Image</th>
                            @if($canEditBrand || $canDeleteBrand)
                            <th class="text-end">Status</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($brands as $brand)
                          <tr>
                            <td class="text-center">

                              <h6 class="mb-0">{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</h6>

                            </td>

                            <td class="text-center">{{ $brand->brand_name }}</td>
                            <td class="text-center">
                              <img src="https://beehiiv-images-production.s3.amazonaws.com/uploads/asset/file/0bea3124-209e-4793-8ac2-d5224ee85d81/image.png?t=1708639949" alt="Product Image" width="60" height="60">

                            </td>
                            @if($canEditBrand || $canDeleteBrand)
                            <td class="text-end">
                              @if($canEditBrand)
                              <a href="{{ route('admin.brandEdit', $brand->id) }}"
                                 class="btn btn-icon btn-md" data-bs-toggle="tooltip"
                                 title="Edit">
                                <i class="ti ti-pencil"></i>
                              </a>
                              @endif

                              @if($canDeleteBrand)
                              <form action="{{ route('admin.brandDestroy', $brand->id) }}"
                                    method="POST"
                                    class="delete-brand-form"
                                    style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-icon btn-md text-danger"
                                        data-bs-toggle="tooltip" title="Delete">
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
                              Brand found.</td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pb-1 px-3">
                      {{ $brands->links('pagination::simple-bootstrap-5') }}
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

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.delete-brand-form').forEach(form => {
        form.addEventListener('submit', function (e) {
          e.preventDefault();

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
              form.submit(); // Only submit if confirmed
            }
          });
        });
      });
    });
  </script>


</body>

@endsection