@extends('layouts.layout')

@section('content')

@php
    $adminUser = auth()->guard('admin')->user();
    $canViewRetailer = $adminUser && $adminUser->hasPermission('retailer', 'read');
    $canEditRetailer = $adminUser && $adminUser->hasPermission('retailer', 'update');
    $canDeleteRetailer = $adminUser && $adminUser->hasPermission('retailer', 'delete');
@endphp
<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Retailer List</h1>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route ('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Retailer</li>
            <li class="breadcrumb-item">Retailer List</li>
          </ul>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12">
                <div class="card table-card">
                  <div class="card-header card-header d-flex align-items-center justify-content-between">
                    <h5 class="flex-grow-1">All Retailer</h5>
                  </div>
                  <div class="card-body shadow rounded-3">
                    <div class="table-responsive">
                      <table class="table table-striped mb-0" id="pc-dt-export">
                        <thead class="table-light">
                          <tr>
                            <th class="text-center">ID</th>
                            <th class="text-start">Name</th>
                            <th class="text-start">Address</th>
                            <th class="text-center">Mobile</th>
                            <th class="text-start">Email</th>
                            @if($canEditRetailer || $canDeleteRetailer)
                            <th class="text-center">Status</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>
                          @forelse ($retailers as $retailer)
                          <tr>
                            <td class="text-center">
                              <h6 class="mb-0">{{ $loop->iteration + ($retailers->currentPage() - 1) * $retailers->perPage() }}</h6>
                            </td>
                            <td class="text-start">
                                @if ($retailer->firstname || $retailer->lastname)
                                    {{ ucfirst($retailer->firstname) }} {{ ucfirst($retailer->lastname) }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-start">{{ $retailer->address1 ?? 'N/A' }}</td>
                            <td class="text-center">{{ $retailer->mobile_no ?? 'N/A' }}</td>
                            <td class="text-start">{{ $retailer->email ?? 'N/A' }}</td>
                            @if($canEditRetailer || $canDeleteRetailer)
                            <td class="text-end">
                              @if($canEditRetailer)
                              <a href="{{route('admin.retailerEdit', $retailer->id)}}"
                                 class="btn btn-icon btn-md" data-bs-toggle="tooltip"
                                 title="Edit">
                                <i class="ti ti-pencil"></i>
                              </a>
                               @endif
                              
                              @if($canDeleteRetailer)
                              <form
                                    action="{{ route('admin.retailerDestroy', $retailer->id) }}"
                                    method="POST"
                                    class="deleteRetailerForm d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-icon btn-md text-danger deleteRetailerBtn"
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
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No
                              Retailer found.</td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                        
                    </div>
                    <div class="d-flex justify-content-end mt-4 pb-1 px-3">
                      {{ $retailers->links('pagination::simple-bootstrap-5') }}
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


<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.deleteRetailerForm');

    deleteForms.forEach(form => {
      form.addEventListener('submit', function (e) {
        e.preventDefault(); // Stop the form from submitting immediately

        Swal.fire({
          title: 'Are you sure?',
          text: "This action cannot be undone!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit(); // Proceed to submit the form
          }
        });
      });
    });
  });
</script>

</body>


@endsection