@extends('layouts.layout')

@section('content')
@php
use Illuminate\Support\Str;
@endphp

<div class="page-content-wrapper">
  <div class="content-container">
    <div class="page-content">
      <div class="content-header">
        <h1>Staff List</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item">Staff</li>
          <li class="breadcrumb-item">Staff List</li>
        </ul>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="row">
            <div class="col-12">
              <div class="card table-card">
                <div class="card-header card-header d-flex align-items-center justify-content-between">
                  <h5 class="flex-grow-1">All Staff</h5>
                  <div class="btn-group">
                    <a href="{{ route('admin.export.csv') }}" class="btn btn-light-primary btn-sm">Export CSV</a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="pc-dt-export">
                      <thead>
                        <tr>
                          <th class="text-start">ID</th>
                          <th class="text-start">Name</th>
                          <th class="text-start">Mobile</th>
                          <th class="text-start">Email</th>
                          <th class="text-start">Role</th>
                          <!-- <th class="text-center">Status</th> -->
                          @php
                          $adminUser = auth()->guard('admin')->user();
                          $canEditStaff = $adminUser && $adminUser->hasPermission('staff', 'update');
                          $canDeleteStaff = $adminUser && $adminUser->hasPermission('staff', 'delete');
                          @endphp

                          @if($canEditStaff || $canDeleteStaff)
                          <th class="text-center">Status</th>
                          @endif

                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($admins as $admin)
                        @if($admin->id == 1)
                        @continue
                        @endif
                        <tr @if(isset($currentAdmin) && $admin->id === $currentAdmin->id) class="table-success" @endif>
                          <td class="text-center">

                            <h6 class="mb-0">{{ $loop->iteration + ($admins->currentPage() - 1) * $admins->perPage() }}
                            </h6>

                          </td>
                          <td class="text-start">
                            {{ Str::title($admin->firstname) }} {{ Str::title($admin->lastname) }}
                          </td>
                          <td class="text-start">{{ $admin->mobile_no }}</td>
                          <td class="text-start">{{ $admin->email }}</td>
                          <td class="text-start">{{ $admin->role->name ?? 'N/A' }}</td>
                          @if($canEditStaff || $canDeleteStaff)
                          <td class="text-center">

                            @if($canEditStaff)
                            <a href="{{ route('admin.edit' , $admin->id) }}" class="btn btn-icon btn-md" data-bs-toggle="tooltip" title="Edit">
                              <i class="ti ti-pencil"></i>
                            </a>
                            @endif

                            @if($canDeleteStaff)
                            <form action="{{ route('admin.destroy', $admin->id) }}" method="POST"
                                  class="delete-admin-form" data-admin-name="{{ $admin->firstname }} {{ $admin->lastname }}"
                                  style="display:inline-block;">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-icon btn-md text-danger" data-bs-toggle="tooltip"
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
                          <!-- <td colspan="5" class="px-6 py-4 text-center text-gray-500">No Staff found.</td> -->
                          <td colspan="{{ ($canEditStaff || $canDeleteStaff) ? 6 : 5 }}" class="px-6 py-4 text-center text-gray-500">No Staff found.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                  <div class="d-flex justify-content-end mt-4 pb-1 px-3">
                    {{ $admins->links('pagination::simple-bootstrap-5') }}
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
  document.querySelectorAll('.delete-admin-form').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault(); // prevent immediate submit

      const adminName = this.getAttribute('data-admin-name');

      Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${adminName}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          this.submit();
        }
      });
    });
  });
</script>

@endsection