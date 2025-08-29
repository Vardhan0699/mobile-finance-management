@extends('layouts.layout')

@section('content')

<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Pending List</h1>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route ('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Customer</li>
            <li class="breadcrumb-item">Pending List</li>
          </ul>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12">
                <div class="card table-card">
                  <div class="card-header card-header d-flex align-items-center justify-content-between">
                    <h5 class="flex-grow-1">All Pending List</h5>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table" id="pc-dt-export">
                        <thead>
                          <tr>
                            <th class="text-center">ID</th>
                            <th class="text-start">Retailer Name</th>
                            <th class="text-start">Customer Name</th>
                            <th class="text-center">Emi</th>
                            <th class="text-center">Downpayment</th>
                            <th class="text-end">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($customers as $customer)
                          <tr>
                            <td class="text-center">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                            <td class="text-start">{{ $customer->retailer_firstname }} {{ $customer->retailer_lastname }}</td>
                            <td class="text-start">{{ $customer->customer_firstname }} {{ $customer->customer_lastname }}</td>
                            <td class="text-center">{{ $customer->emi }}</td>
                            <td class="text-center">{{ $customer->downpayment }}</td>
                            <td class="text-end">
                              @if ($customer->status == 0)
                              <form method="POST" action="{{ route('admin.customer.approve', $customer->id) }}" class="approve-form" id="approve-form-{{ $customer->id }}">
                                @csrf
                                <button type="button" class="btn btn-warning btn-sm approve-btn" data-id="{{ $customer->id }}">Pending</button>
                              </form>
                              @else
                              <button class="btn btn-success btn-sm" disabled>Approved</button>
                              @endif

                            </td>
                          </tr>
                          @endforeach

                        </tbody>
                      </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pb-1 px-3">
                      {{ $customers->links('pagination::simple-bootstrap-5') }}
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


  <!-- SweetAlert2 Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>

  <script>

    // SweetAlert for success and error messages
    @if(session('success'))
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6'
      });
    @endif

    @if(session('error'))
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33'
      });
    @endif


  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const approveButtons = document.querySelectorAll('.approve-btn');

      approveButtons.forEach(button => {
        button.addEventListener('click', function () {
          const customerId = this.getAttribute('data-id');
          Swal.fire({
            title: 'Are you sure?',
            text: "You want to approve this customer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              document.getElementById('approve-form-' + customerId).submit();
            }
          });
        });
      });
    });
  </script>


</body>


@endsection