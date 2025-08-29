@extends('retailerLogin.layout.layout')

@section('content')

<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1>Customer List</h1>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Customer</li>
            <li class="breadcrumb-item">Customer List</li>
          </ul>
        </div>

        @if (session('success'))
        <div class="alert alert-success" id="success-popup">
          {{ session('success') }}
        </div>

        <script>
          setTimeout(function () {
            var popup = document.getElementById('success-popup');
            if (popup) {
              popup.remove();
            }
          }, 3000);
        </script>
        @endif
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12">
                <div class="card table-card">
                  <div class="card-header card-header d-flex align-items-center justify-content-between">
                    <h5 class="flex-grow-1">All Customer</h5>
                    <div class="btn-group">
                      <a href="{{ route('retailer.customers_exportExcel') }}"
                         class="btn btn-light-primary btn-sm">Export CSV</a>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped mb-0" id="pc-dt-export">
                        <thead class="table-light">
                          <tr>
                            <th class="text-center">#</th>
                            <th class="text-start">Customer Name</th>
                            <th class="text-center">Aadhaar Number</th>
                            <th class="text-center">Mobile</th>
                            <th class="text-center">Date of Birth</th>
                            <th class="text-center">Address</th>
                            <th class="text-center">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse ($customers as $customer)
                          <tr>
                            <td class="text-center">
                              <h6 class="mb-0">
                                {{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</h6>
                            </td>
                            <td class="text-start">
                              {{ ucfirst(strtolower($customer->customer_firstname ?? '-')) }}
                              {{ ucfirst(strtolower($customer->customer_lastname ?? '-')) }}
                            </td>
                            <td class="text-center">{{ $customer->aadhaar_number ?? 'N/A' }}</td>
                            <td class="text-center">{{ $customer->mobile ?? 'N/A' }}</td>
                            <!-- <td class="text-center">
{{ optional($customer->loans->first()->brand)->brand_name ?? 'N/A' }}
</td>
<td class="text-center">
{{ optional($customer->loans->first()->product)->product_name ?? 'N/A' }}
</td> -->

                            <td class="text-center">{{ $customer->date_of_birth ?? 'N/A' }}</td>
                            <td class="text-center">{{ $customer->address1 ?? 'N/A' }}</td>
                            <td class="text-center">
                              <a href="{{route('retailer.customer_data', $customer->id)}}" class="btn btn-icon btn-md"
                                 data-bs-toggle="tooltip" title="Show">
                                <i class="ti ti-eye"></i>
                              </a>

                              <!-- Edit button only shown if status is 0 
                              @if ($customer->status == 1)

                              <a href="{{ route('retailer.customerEdit', $customer->id) }}"
                                 class="btn btn-icon btn-md" data-bs-toggle="tooltip"
                                 title="Edit">
                                <i class="ti ti-pencil"></i>
                              </a>
                              @endif -->


                              <!--   <form action="{{ route('retailer.customerDestory', $customer->id) }}"
method="POST"
class="delete-form"
data-id="{{ $customer->id }}"
style="display:inline-block;">
@csrf
@method('DELETE')
<button type="button" class="btn btn-icon btn-md text-danger delete-btn" title="Delete">
<i class="ti ti-archive"></i>
</button>
</form> -->

                            </td>


                            <td class="text-center" hidden>{{ $customer->retailer_id }}</td>
                            <!-- Display retailer_id -->
                          </tr>
                          @empty
                          <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No Customer List</td>
                          </tr>
                          @endforelse
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
</body>

<!-- Bootstrap JS (needed for dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const form = btn.closest('.delete-form');

        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
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


@endsection