@extends('layouts.layout')

@section('content')

<div class="page-content-wrapper theme-1">
  <div class="content-container">
    <div class="page-content">
      <div class="content-header">
        <h1 class="h4">Permission</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Role</li>
          <li class="breadcrumb-item active"><a href="{{ route('admin.role_index') }}">Add Role</a></li>
          <li class="breadcrumb-item active">Permission</li>
        </ul>
      </div>

      <div class="container py-4">
        <div class="row justify-content-center">
          <div class="col-12 col-md-10 col-lg-12">

            <div class="d-flex justify-content-between mb-3">
              <h4 class="mb-0 text-capitalize text-break">Role: {{ $role->name }}</h4>
              <a href="{{ route('admin.role_index') }}" class="btn btn-primary">Back</a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <form action="{{ route('admin.permission_update', $role->id) }}" method="POST">
              @csrf

              {{-- Select All Checkbox --}}
              <div class="form-check mb-3">
                <input type="checkbox" id="selectAll" class="form-check-input" style="border-color: black;">
                <label for="selectAll" class="form-check-label">Select All Permissions</label>
              </div>

              <div class="card border-0 shadow rounded-3">
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-striped mb-0">
                      <thead class="table-light">
                        <tr>
                          <th class="text-start">Page Name</th>
                          <th class="text-center">All</th>
                          <th class="text-center">Read</th>
                          <th class="text-center">Write</th>
                          <th class="text-center">Update</th>
                          <th class="text-center">Delete</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($pages as $page)
                        @php
                          $assigned = $existingPermissions[$page] ?? [];
                          $oldPermissions = old("permissions.$page");
                          $checkArray = is_array($oldPermissions) ? $oldPermissions : $assigned;
                        @endphp
                        <tr>
                          <td class="text-capitalize text-start">{{ $page }}</td>

                          {{-- All Checkbox --}}
                          <td class="text-center">
                            <input type="checkbox" class="checkbox all-checkbox">
                          </td>

                          @foreach (['read', 'write', 'update', 'delete'] as $perm)
                          <td class="text-center">
                            <input type="checkbox" name="permissions[{{ $page }}][{{ $perm }}]"
                                   value="1" class="checkbox permission-checkbox"
                                   {{ in_array($perm, $checkArray) ? 'checked' : '' }}>
                          </td>
                          @endforeach
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success">Save Permissions</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- Select All Script --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="permissions"]');

    // Handle clicking "Select All"
    selectAllCheckbox.addEventListener('change', function () {
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
      });
    });

    // Update "Select All" state when any individual checkbox changes
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function () {
        const total = checkboxes.length;
        const checked = document.querySelectorAll('input[type="checkbox"][name^="permissions"]:checked').length;

        if (checked === 0) {
          selectAllCheckbox.checked = false;
          selectAllCheckbox.indeterminate = false;
        } else if (checked === total) {
          selectAllCheckbox.checked = true;
          selectAllCheckbox.indeterminate = false;
        } else {
          selectAllCheckbox.checked = false;
          selectAllCheckbox.indeterminate = true;
        }
      });
    });

    // Trigger initial state
    const initChecked = document.querySelectorAll('input[type="checkbox"][name^="permissions"]:checked').length;
    if (initChecked > 0 && initChecked < checkboxes.length) {
      selectAllCheckbox.indeterminate = true;
    } else {
      selectAllCheckbox.checked = initChecked === checkboxes.length;
      selectAllCheckbox.indeterminate = false;
    }
  });
</script>

{{-- Row "All" Checkbox Sync Script --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('table');

    table.querySelectorAll('tr').forEach(row => {
      const allCheckbox = row.querySelector('.all-checkbox');
      const permissionCheckboxes = row.querySelectorAll('.permission-checkbox');

      if (allCheckbox && permissionCheckboxes.length) {
        allCheckbox.addEventListener('change', function () {
          permissionCheckboxes.forEach(cb => cb.checked = this.checked);
        });

        permissionCheckboxes.forEach(cb => {
          cb.addEventListener('change', function () {
            if (!this.checked) {
              allCheckbox.checked = false;
            } else {
              const allChecked = [...permissionCheckboxes].every(cb => cb.checked);
              allCheckbox.checked = allChecked;
            }
          });
        });

        // Initial state
        const allCheckedInit = [...permissionCheckboxes].every(cb => cb.checked);
        allCheckbox.checked = allCheckedInit;
      }
    });
  });
</script>

@endsection
