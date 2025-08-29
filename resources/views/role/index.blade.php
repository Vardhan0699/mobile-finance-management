@extends('layouts.layout')

@section('content')
@php
$adminUser = auth()->guard('admin')->user();
$canViewRole = $adminUser && $adminUser->hasPermission('role', 'read');
$canWriteRole = $adminUser && $adminUser->hasPermission('role', 'write');
$canEditRole = $adminUser && $adminUser->hasPermission('role', 'update');
$canDeleteRole = $adminUser && $adminUser->hasPermission('role', 'delete');
@endphp
<div class="page-content-wrapper theme-1">
  <div class="content-container">
    <div class="page-content">
      <div class="content-header">
        <h1 class="h4">Roles</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Roles</li>
          <li class="breadcrumb-item active">Add Roles</li>
        </ul>
      </div>

      <div class="container py-4">
        <div class="row justify-content-center">
          <div class="col-lg-12">

            <!-- New Role Button -->
            <div class="d-flex justify-content-between mb-3">
              <h4 class="mb-0">Role List</h4>
              @if($canWriteRole)
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                + New Role
              </button>
              @endif
            </div>

            <!-- Role List Table -->
            <div class="card-body shadow rounded-3">
                    <div class="table-responsive">
                      <table class="table table-striped mb-0" id="pc-dt-export">
                        <thead class="table-light">
                    <tr>
                      <th class="text-center">#</th>
                      <th class="text-center">Role Name</th>
                      <th class="text-center">Created At</th>
                      @if($canEditRole || $canDeleteRole)
                      <th class="text-center">Status</th>
                      @endif
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($roles as $index => $role)
                    @if ($role->id != 1)
                    {{-- maybe this is skipped by mistake --}}
                    <tr>
                      <td class="text-center">{{ $index + 1 }}</td>
                      <td class="text-center">
                        @if($adminUser && $adminUser->hasPermission('permission', 'write'))
                        <a href="{{ route('admin.permission_index', $role->id) }}">{{ $role->name }}</a>
                        @else
                        {{ $role->name }}
                        @endif
                      </td>
                      <td class="text-center">{{ $role->created_at->format('d M Y') }}</td>
                      @if($canEditRole || $canDeleteRole)
                      <td class="text-center">
                        @if($canEditRole)
                        <a href="javascript:void(0);" class="btn btn-icon btn-md edit-role-btn"
                           data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}" data-bs-toggle="tooltip"
                           title="Edit">
                          <i class="ti ti-pencil"></i>
                        </a>
                        @endif

                        @if($canDeleteRole)
                        <form action="{{ route('admin.role_destroy', $role->id) }}" method="POST"
                              class="delete-role-form" data-role-name="{{ $role->name }}" style="display:inline-block;">
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
                    @endif
                    @empty
                    <tr>
                      <td colspan="3" class="text-center">No roles found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Create Role Modal -->
            <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel"
                 aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <form action="{{  route('admin.role_store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter role name" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Save</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>

            <!-- End Create Modal -->

            <!-- Edit Role Modal -->
            <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel"
                 aria-hidden="true">
              <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <form id="editRoleForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editRoleName" class="form-control" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Update</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>
            <!-- End Modal -->

          </div>
        </div>
      </div>

    </div>
  </div>
</div>


<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom style for positioning -->
<style>
  .fl-wrapper {
    margin-top: 60px !important;
    top: 2px;
    margin-bottom: 0 !important;
  }
</style>

<!-- SweetAlert: Success Message -->
@if(session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: '{{ session('title') ?? "Success!" }}',
    text: '{{ session('success') }}',
    toast: true,
    position: 'top-end',
    showConfirmButton: true,
    customClass: {
    popup: 'custom-toast'
  }
            });
</script>
@endif


<!-- SweetAlert: Delete Confirmation -->
<script>
  document.querySelectorAll('.delete-role-form').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault(); // prevent form from submitting

      const roleName = this.getAttribute('data-role-name');

      Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the role "${roleName}".`,
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


  // Edit Role Modal Open & Populate 
  document.querySelectorAll('.edit-role-btn').forEach(button => {
    button.addEventListener('click', function () {
      const roleId = this.getAttribute('data-role-id');
      const roleName = this.getAttribute('data-role-name');

      // Set form action url dynamically (update route)
      const form = document.getElementById('editRoleForm');
      form.action = `/admin/roles/${roleId}`; // Adjust to your route pattern

      // Set input value
      document.getElementById('editRoleName').value = roleName;

      // Show modal
      const editModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
      editModal.show();
    });
  });
</script>

@endsection