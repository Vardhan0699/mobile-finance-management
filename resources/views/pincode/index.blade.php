@extends('layouts.layout')

@section('content')

@php
$adminUser = auth()->guard('admin')->user();
$canViewPincode = $adminUser && $adminUser->hasPermission('approved pincode', 'read');
$canWritePincode = $adminUser && $adminUser->hasPermission('approved pincode', 'write');
$canEditPincode = $adminUser && $adminUser->hasPermission('approved pincode', 'update');
$canDeletePincode = $adminUser && $adminUser->hasPermission('approved pincode', 'delete');
@endphp

<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="d-flex justify-content-between align-items-center pb-4">
          <div class="content-header">
            <h1>Pincode List</h1>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route ('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item">Approve Pincode</li>
              <li class="breadcrumb-item">Pincode List</li>
            </ul>
          </div>

          @if($canWritePincode)
          <div class="text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPincodeModal">
              Add Pincode
            </button>
          </div>
          @endif
        </div>
        {{-- Success Message --}}
          @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          {{-- Error Messages --}}
          @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12">
                <div class="card table-card">
                  <div
                       class="card-header card-header d-flex align-items-center justify-content-between">
                    <h5 class="flex-grow-1">All Pincode</h5>
                  </div>
                  <div class="card-body shadow rounded-3">
                    <div class="table-responsive">
                      <table class="table table-striped mb-0" id="pc-dt-export">
                        <thead class="table-light">
                          <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Pincode</th>
                            @if($canEditPincode || $canDeletePincode)
                            <th class="text-center">Status</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($pincodes as $pin)
                          <tr>
                            <td class="text-center">
                              <div
                                   class="d-flex align-items-center justify-content-center">
                                <h6 class="mb-0">{{ $loop->iteration + ($pincodes->currentPage() - 1) * $pincodes->perPage() }}</h6>
                              </div>
                            </td>
                            <td class="text-center">
                              {{ optional($pin)->pincode ?? 'No Pincode' }}
                            </td>

                            @if($canEditPincode || $canDeletePincode)
                            <td class="text-center">
                              @if($canEditPincode)
                              <a href="javascript:void(0);"
                                 class="btn btn-icon btn-md editPincodeBtn"
                                 data-id="{{ $pin->id }}"
                                 data-pincode="{{ $pin->pincode }}" title="Edit">
                                <i class="ti ti-pencil"></i>
                              </a>
                              @endif

                              @if($canDeletePincode)
                              <form
                                action="{{ route('admin.pincodedDestory', $pin->id) }}"
                                method="POST"
                                class="deletePincodeForm d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-icon btn-md text-danger deletePincodeBtn"
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
                              brands found.</td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pb-1 px-3">
                      {{ $pincodes->links('pagination::simple-bootstrap-5') }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="modal fade" id="addPincodeModal" tabindex="-1" aria-labelledby="addPincodeModalLabel"
             aria-hidden="true">
          <div class="modal-dialog">
            <form action="{{ route('admin.pincodeStore') }}" method="POST">
              @csrf
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addPincodeModalLabel">Add New Pincode</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                          aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="pincode-name" class="form-label">Pincode</label>
                    <input type="text"
                           class="form-control @error('pincode') is-invalid @enderror"
                           id="pincode-name"
                           name="pincode"
                           pattern="\d{6}"
                           maxlength="6"
                           title="Please enter a valid 6-digit pincode"
                           inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                           required>
                  </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary"
                          data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>


        <div class="modal fade" id="editPincodeModal" tabindex="-1" aria-labelledby="editPincodeModalLabel"
             aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST"  id="editPincodeForm" action="{{ route('admin.pincodeUpdate', $pincode->id ?? 0) }}">
              @csrf
              @method('PUT')
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editPincodeModalLabel">Edit Pincode</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                          aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" id="editPincodeId">
                  <div class="mb-3">
                    <label for="editPincodeInput" class="form-label">Pincode</label>
                    <input type="text" class="form-control" id="editPincodeInput" name="pincode"
                           pattern="\d{6}" maxlength="6" minlength="6" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary"
                          data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Update</button>
                </div>
              </div>
            </form>
          </div>
        </div>


      </div>
    </div>
  </div>

  <!-- Bootstrap JS (with Popper included) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Bootstrap JS (needed for dropdown) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- ✅ DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    @if ($errors->has('pincode'))
      var myModal = new bootstrap.Modal(document.getElementById('addPincodeModal'));
    myModal.show();
    @endif
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const editButtons = document.querySelectorAll('.editPincodeBtn');
      editButtons.forEach(button => {
        button.addEventListener('click', function () {
          const id = this.dataset.id;
          const pincode = this.dataset.pincode;

          document.getElementById('editPincodeId').value = id;
          document.getElementById('editPincodeInput').value = pincode;

          const form = document.getElementById('editPincodeForm');
          form.action = `/admin/pincode/${id}/update`;

          const modal = new bootstrap.Modal(document.getElementById('editPincodeModal'));
          modal.show();
        });
      });

      // Initialize DataTable
      $('#pc-dt-export').DataTable({
        pageLength: 10, // Show 10 entries per page
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        responsive: true
      });
    });
  </script>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.deletePincodeForm').forEach(form => {
      form.addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
          title: 'Are you sure?',
          text: 'This action will delete the pincode permanently!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  });
</script>

</body>

@endsection