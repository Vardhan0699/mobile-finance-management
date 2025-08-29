@extends('layouts.layout')

@section('content')

@php
$adminUser = auth()->guard('admin')->user();
$canViewCustomer = $adminUser && $adminUser->hasPermission('customer', 'read');
$canEditCustomer = $adminUser && $adminUser->hasPermission('customer', 'update');
$canDeleteCustomer = $adminUser && $adminUser->hasPermission('customer', 'delete');
@endphp

<div class="page-content-wrapper">
  <div class="content-container">
    <div class="page-content">
      <div class="content-header">
        <h1>Customer List</h1>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item">Customer</li>
          <li class="breadcrumb-item">Customer List</li>
        </ul>
      </div>

      @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
      @endif

      <div class="row">
        <div class="col-12">
          <div class="card table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="mb-0">All Customers</h5>
              <a href="{{ route('admin.customers_exportExcel') }}" class="btn btn-light-primary btn-sm">Export CSV</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped mb-0" id="CustomerList">
                  <thead class="table-light">
                    <tr>
                      <th class="text-start">Customer</th>
                      <th>Mobile</th>
                      <th>Retailer</th>
                      <th>Aadhar Number</th>
                      <th>Date of birth</th>
                      <th>Created Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($customers as $customer)
                    <tr>
                      <td>
                        
                          
                          <div class="ms-3">
                            <span class="fw-semibold">
                              {{ ucfirst(strtolower($customer->customer_firstname)) }} {{ ucfirst(strtolower($customer->customer_lastname)) }}
                            </span>
                          </div>
                        
                      </td>
                      <td>{{ $customer->mobile ?? 'N/A' }}</td>
                      <td>
                        @php
                        $retailer = $customer->retailer;
                        $isRetailerValid = $retailer && $retailer->firstname !== '-' && $retailer->lastname !== '-';
                        @endphp
                        {{ $isRetailerValid ? ucfirst(strtolower($retailer->firstname)) . ' ' . ucfirst(strtolower($retailer->lastname)) : 'N/A' }}
                      </td>
                      <td>{{ $customer->aadhaar_number ?? 'N/A' }}</td>
                      <td>{{ $customer->date_of_birth ?? 'N/A' }}</td>
                      <td>{{ $customer->created_at ? $customer->created_at->format('d/m/Y') : 'N/A' }}</td>
                      
                      <td>
                        @if($canViewCustomer)
                        <a href="{{route('admin.customer_show', $customer->id)}}" class="btn btn-icon btn-md" data-bs-toggle="tooltip" title="Show">
                            <i class="ti ti-eye"></i>
                            </a>
                        @endif
                        
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="8" class="text-center text-muted">No Customers found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-end mt-3">
                {{ $customers->links('pagination::simple-bootstrap-5') }}
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
