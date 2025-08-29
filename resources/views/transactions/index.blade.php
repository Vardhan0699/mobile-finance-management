@extends('layouts.layout')

@section('content')

  @php
    $admin = Auth::guard('admin')->user();
    $today = now()->toDateString();
    @endphp

  @php
    $adminUser = auth()->guard('admin')->user();
    $canViewTransaction = $adminUser && $adminUser->hasPermission('transactions', 'read');
    $canWriteTransaction = $adminUser && $adminUser->hasPermission('transactions', 'write');
    $canEditTransaction = $adminUser && $adminUser->hasPermission('transactions', 'update');
    $canDeleteTransaction = $adminUser && $adminUser->hasPermission('transactions', 'delete');
    @endphp

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    .swal-wide {
    width: 70% !important;
    max-width: 70% !important;
    }
  </style>

  <div class="page-content-wrapper">
    <div class="content-container">
    <div class="page-content">

      <!-- Header -->
      <div class="content-header">
      <h1>Transactions List</h1>
      <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('retailer.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Transactions</li>
      </ul>
      </div>

      <!-- Summary Cards -->
      <div class="row mb-4">
      <div class="col-md-4">
        <div class="card bg-primary text-white">
        <div class="card-body text-center">
          <h5 class="card-title">Total Amount (₹)</h5>
          <h3 class="mb-0">{{ number_format($totalAmount ?? 0, 2) }}</h3>
        </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-success text-white">
        <div class="card-body text-center">
          <h5 class="card-title">Paid Amount (₹)</h5>
          <h3 class="mb-0">{{ number_format($paidAmount ?? 0, 2) }}</h3>
        </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-danger text-white">
        <div class="card-body text-center">
          <h5 class="card-title">Unpaid Amount (₹)</h5>
          <h3 class="mb-0">{{ number_format($unpaidAmount ?? 0, 2) }}</h3>
        </div>
        </div>
      </div>
      </div>


      <!-- Filter Button & Add Transaction -->
      <div class="d-flex justify-content-between align-items-center mb-4">
      <button type="button" class="btn btn-primary" onclick="openFilterModal()">Filter</button>
      <div class="btn-group">
        <a href="{{ route('admin.transactions.export.csv') }}" class="btn btn-light-primary btn-sm">Export CSV</a>
      </div>

      @if($canWriteTransaction)
      <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal"
      onclick="resetForm()">Add Transaction</a>
    @endif
      </div>

      <!-- Hidden Original Filter Form (fallback) -->
      <div id="hidden-filter-form" style="display: none;">
      <form method="GET" action="{{ route('admin.transactions.index') }}" class="transparent-form w-100 mb-4">
        <div class="row g-3 align-items-end">

        <div class="col-md-2">
          <label class="form-label text-dark">Filter By User</label>
          <select name="by" class="form-select transparent-select" onchange="this.form.submit()">
          <option value="">All Users</option>
          @foreach ($users as $user)
        <option value="{{ $user }}" {{ request('by') == $user ? 'selected' : '' }}>{{ $user }}</option>
      @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label text-dark">Filter By Status</label>
          <select name="status" class="form-select transparent-select" onchange="this.form.submit()">
          <option value="">All Status</option>
          <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
          <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label text-dark">Search by Mobile</label>
          <input type="text" name="mobile" class="form-control transparent-select" placeholder="Enter mobile number"
          value="{{ request('mobile') }}" onchange="this.form.submit()">
        </div>

        <div class="col-md-2">
          <label class="form-label text-dark">From Date</label>
          <input type="date" name="date_from" class="form-control transparent-select" max="{{ date('Y-m-d') }}"
          value="{{ request('date_from') }}" onchange="this.form.submit()">
        </div>

        <div class="col-md-2">
          <label class="form-label text-dark">To Date</label>
          <input type="date" name="date_to" class="form-control transparent-select" max="{{ date('Y-m-d') }}"
          value="{{ request('date_to') }}" onchange="this.form.submit()">
        </div>

        </div>
      </form>
      </div>



      <!-- Transactions Table -->
      <div class="table-responsive">
      <table class="table table-bordered align-middle text-nowrap">
        <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Transaction ID</th>
          <th class="text-center">Amount</th>
          <th class="text-center">Mobile No</th>
          <th class="text-center">Trans Date</th>
          <th class="text-center">Status</th>
          <th class="text-center">Payment Date</th>
          <th class="text-center">By</th>
          @if($canEditTransaction || $canDeleteTransaction)
        <th>Actions</th>
      @endif
        </tr>
        </thead>
        <tbody>
        @forelse ($transactions as $transaction)
        <tr id="transaction-row-{{ $transaction->id }}">
        <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
        <td>{{ $transaction->trans_id }}</td>
        <td class="text-center">{{ $transaction->amount }} </td>
        <td class="text-center">{{ $transaction->mobile_no ?? '-' }}</td>
        <td class="text-center">{{ \Carbon\Carbon::parse($transaction->trans_date)->format('d-m-Y') }}</td>
        <td class="text-center">
        <span class="badge bg-{{ $transaction->status === 'paid' ? 'success' : 'danger' }}">
          {{ ucfirst($transaction->status) }}
        </span>
        </td>
        <td class="text-center">
        {{ $transaction->paid_date ? \Carbon\Carbon::parse($transaction->paid_date)->format('d-m-Y') : '-' }}
        </td>
        <td class="text-center">
        @if($transaction->staff)
        {{ $transaction->staff->firstname }} {{ $transaction->staff->lastname }}<br>

      @else
        {{ $transaction->by }}
      @endif
        </td>
        @if($canEditTransaction || $canDeleteTransaction)
        <td class="d-flex flex-wrap gap-1">
        @php
        $isToday = \Carbon\Carbon::parse($transaction->trans_date)->isToday();
        @endphp

        @if($admin->role->id == 1 || $isToday)
        @if($canEditTransaction)
        <button onclick="editTransaction({{ $transaction->id }})" class="btn btn-sm btn-warning">Edit</button>
        @endif

        @if($canDeleteTransaction)
        <button onclick="deleteTransaction({{ $transaction->id }})"
        class="btn btn-sm btn-danger">Delete</button>
        @endif
      @endif

        @if($canWriteTransaction)
        @if ($transaction->status === 'unpaid')
        <button onclick="markAsPaid({{ $transaction->id }})" class="btn btn-sm btn-success">Mark as
        Paid</button>
        @endif
      @endif
        </td>
      @endif
        </tr>
      @empty
      <tr>
        <td colspan="9" class="text-center">No transactions found.</td>
      </tr>
      @endforelse
        </tbody>
      </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3">
      {{ $transactions->links('pagination::bootstrap-5') }}
      </div>

    </div>
    </div>
  </div>



  <!-- Modal -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <form id="transactionForm" class="w-100">
      @csrf
      <input type="hidden" name="transaction_id" id="transaction_id" value="">
      <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transactionModalTitle">Add Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
        <label>Amount <span class="text-danger">*</span></label>
        <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter amount" required>
        </div>
        <div class="mb-3">
        <label>Transaction ID</label>
        <input type="text" name="transactionId" id="transactionId" class="form-control"
          placeholder="Enter transaction ID">
        </div>
        <div class="mb-3">
        <label>Date <span class="text-danger">*</span></label>
        @if($admin->role->id == 1)
      <!-- Super Admin: Can select any date -->
      <input type="date" name="trans_date" id="date" class="form-control" placeholder="Select any date" required
        value="{{ $today }}">
      @else
      <!-- Other Roles: Only today and yesterday allowed -->
      <input type="date" name="trans_date" id="limitedDate" class="form-control" placeholder="Select date"
        max="{{ date('Y-m-d') }}" required value="{{ $today }}">
      @endif

        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary w-100" id="submitTransactionBtn">Save Transaction</button>
      </div>
      </div>
    </form>
    </div>
  </div>

  <script>
    window.currentUserRoleId = @json(Auth::guard('admin')->user()->role_id);
  </script>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>


    // Limit date inputs
    document.addEventListener("DOMContentLoaded", function () {
    const limitedInput = document.getElementById("limitedDate");
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);

    const formatDate = (date) => date.toISOString().split('T')[0];
    if (limitedInput) {
      limitedInput.min = formatDate(yesterday);
      limitedInput.max = formatDate(today);
    }

    const role = "{{ $admin->role }}";
    const dateInput = document.getElementById('date');
    if (dateInput) {
      if (role !== 'super admin') {
      dateInput.setAttribute('max', formatDate(today));
      } else {
      dateInput.removeAttribute('max');
      }
    }
    });

    const transactionModal = new bootstrap.Modal(document.getElementById('addTransactionModal'));

    // Reset form
    window.resetForm = function () {
    document.getElementById('transactionModalTitle').textContent = "Add Transaction";
    document.getElementById('submitTransactionBtn').textContent = "Save Transaction";
    document.getElementById('transactionForm').reset();
    document.getElementById('transaction_id').value = '';

    // Ensure amount and date fields are editable when adding
    const amountInput = document.getElementById('amount');
    const dateInput = document.getElementById('date') || document.getElementById('limitedDate');

    amountInput.removeAttribute('readonly');
    if (dateInput) dateInput.removeAttribute('readonly');

    transactionModal.show();
    };

    // Submit create/update
    document.getElementById('transactionForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = formData.get('transaction_id');
    let url = id ? `/admin/transactions/${id}` : "{{ route('admin.transactions.store') }}";
    let method = 'POST';
    if (id) formData.append('_method', 'PUT');

    fetch(url, {
      method: method,
      headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })
      .then(async res => {
      const data = await res.json();
      if (!res.ok) throw data;
      return data;
      })
      .then(data => {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: data.success,
        timer: 1500,
        showConfirmButton: false
      }).then(() => location.reload());
      })
      .catch(err => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err.error || 'Failed to save transaction.',
      });
      console.error(err);
      });
    });

    // EDIT TRANSACTION
    window.editTransaction = function (id) {
    fetch(`/admin/transactions/${id}/edit`)
      .then(async res => {
      if (!res.ok) throw new Error(await res.text());
      return res.json();
      })
      .then(data => {
      document.getElementById('transactionModalTitle').textContent = "Edit Transaction";
      document.getElementById('submitTransactionBtn').textContent = "Update Transaction";
      document.getElementById('transaction_id').value = id;
      document.getElementById('amount').value = data.amount;
      document.getElementById('transactionId').value = data.trans_id;

      const amountInput = document.getElementById('amount');
      const dateInput = document.getElementById('date') || document.getElementById('limitedDate');
      if (dateInput) dateInput.value = data.trans_date?.substr(0, 10) || '';

      if (data.status === 'paid') {
        amountInput.setAttribute('readonly', 'readonly');
        if (dateInput) dateInput.setAttribute('readonly', 'readonly');
      } else {
        amountInput.removeAttribute('readonly');
        if (dateInput) dateInput.removeAttribute('readonly');
      }

      transactionModal.show();
      })
      .catch(error => {
      Swal.fire('Error', 'Unable to fetch transaction data.', 'error');
      console.error(error);
      });
    };



    // DELETE TRANSACTION
    window.deleteTransaction = function (id) {
    Swal.fire({
      title: 'Are you sure?',
      text: "This will permanently delete the transaction.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
      fetch(`/admin/transactions/${id}`, {
        method: 'DELETE',
        headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
        }
      })
        .then(res => res.json())
        .then(data => {
        if (data.success) {
          Swal.fire('Deleted!', data.success, 'success').then(() => window.location.reload());
        } else {
          Swal.fire('Error!', 'Failed to delete the transaction.', 'error');
        }
        });
      }
    });
    };



    function capitalize(str) {
    if (!str) return 'N/A';
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }

    window.markAsPaid = function (transactionId) {
    Swal.fire({
      title: 'Search Customer',
      input: 'text',
      inputLabel: 'Enter Mobile No / First Name / Loan ID',
      inputPlaceholder: 'Ex: 9876543210 or John or LOAN123',
      showCancelButton: true,
      confirmButtonText: 'Search',
      showLoaderOnConfirm: true,
      preConfirm: (input) => {
      return fetch(`/admin/transactions/verify-customer?search=${encodeURIComponent(input)}`)
        .then(response => {
        if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
        return response.json();
        })
        .catch(error => {
        Swal.showValidationMessage(`Request failed: ${error.message}`);
        });
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
      if (result.isConfirmed && result.value && result.value.success) {
      const customers = result.value.customers;

      let html = '<strong>Select a customer:</strong><ul class="list-group mt-2">';
      customers.forEach(c => {

        let statusText = '';
        switch (c.status) {
        case 1:
          statusText = '<span class="badge bg-success">Active</span>';
          break;
        case 0:
          statusText = '<span class="badge bg-warning text-dark">Inactive</span>';
          break;
        case 2:
          statusText = '<span class="badge bg-danger">Closed</span>';
          break;
        default:
          statusText = '<span class="badge bg-secondary">Unknown</span>';
        }

        html += `<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="cursor:pointer" onclick="showEmiDetails('${c.loanID}', ${transactionId})">
          <span>
            ${capitalize(c.customer_firstname)} ${capitalize(c.customer_lastname)} - ${c.mobile} - ${c.loanID ?? 'N/A'} - ${c.emi ?? 'N/A'}
          </span>
          ${statusText}
        </li>`;
      });

      html += '</ul>';

      Swal.fire({
        title: 'Matching Customers',
        html: html,
        showConfirmButton: false
      });
      } else if (result.isConfirmed) {
      Swal.fire('Not Found', 'No matching customer found.', 'error');
      }
    });
    }

    // Fetch and Show EMI Details
    // window.showEmiDetails = function (customerId, transactionId) {
    // fetch(`/admin/transactions/get-customer-emi-details/${customerId}`)
    //   .then(res => res.json())
    //   .then(data => {
    //   if (data.success) {
    //     const customer = data.customer;
    //     const emiDetails = data.emi_schedule;

    //     let html = `
    // <strong>Customer:</strong> ${capitalize(customer.customer_firstname)} ${capitalize(customer.customer_lastname)}<br>
    // <strong>Loan ID:</strong> ${customer.loanID}<br>
    // <strong>Mobile No:</strong> +91 ${customer.mobile}<br><br>
    // <strong>EMI Schedule:</strong>
    // <table class="table table-bordered mt-2">
    // <thead>
    // <tr>
    // <th>EMI Date</th>
    // <th>Amount</th>
    // <th>Status</th>
    // <th>Action</th>
    // </tr>
    // </thead><tbody>
    // ${emiDetails.map(e => {
    //     const emiAmount = parseFloat(e.amount);
    //     const lateFees = parseFloat(e.late_fees || 0);
    //     const total = emiAmount + lateFees;
    //     const displayAmount = lateFees > 0
    //       ? `₹${total} (₹${emiAmount} + ₹${lateFees} Late Fee)`
    //       : `₹${emiAmount}`;

    //     return `
    // <tr>
    // <td>${new Date(e.emi_date).toLocaleDateString('en-IN')}</td>
    // <td>${displayAmount}</td>
    // <td>${e.status === 'paid' ? 'Paid' : e.status === 'recovery' ? 'Recovery' : 'Unpaid'}</td>
    // <td>
    // ${(e.status === 'unpaid' || e.status === 'recovery') ?
    //       `<button class="btn btn-sm btn-primary" onclick="payEmi(${e.id}, '${customer.mobile}', ${transactionId})">Mark Paid</button>` :
    //       ''}
    // </td>
    // </tr>`;
    //     }).join('')}
    // </tbody></table>`;

    //     Swal.fire({
    //     title: 'EMI Details',
    //     html: html,
    //     showCloseButton: true,
    //     showCancelButton: false,
    //     showConfirmButton: false,
    //     customClass: { popup: 'swal-wide' }
    //     });
    //   } else {
    //     Swal.fire('Error', data.message || 'Failed to fetch EMI details.', 'error');
    //   }
    //   });
    // };


    window.showEmiDetails = function (loanId, transactionId) {
    fetch(`/admin/transactions/get-customer-emi-details/${loanId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const customer = data.customer;
                const loan = data.loan;
                const emiDetails = data.emi_schedule;

                let html = `
                    <strong>Customer:</strong> ${capitalize(customer.customer_firstname)} ${capitalize(customer.customer_lastname)}<br>
                    <strong>Loan ID:</strong> ${loan.loanID}<br>
                    <strong>Mobile No:</strong> +91 ${customer.mobile}<br><br>
                    <strong>EMI Schedule:</strong>
                    <table class="table table-bordered mt-2">
                        <thead>
                            <tr>
                                <th>EMI Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                html += emiDetails.map(e => {
                    const emiAmount = parseFloat(e.amount);
                    const lateFees = parseFloat(e.late_fees || 0);
                    const total = emiAmount + lateFees;
                    const displayAmount = lateFees > 0
                        ? `₹${total} (₹${emiAmount} + ₹${lateFees} Late Fee)`
                        : `₹${emiAmount}`;

                    const statusBadge = e.status === 'paid'
                        ? '<span class="badge bg-success">Paid</span>'
                        : e.status === 'recovery'
                            ? '<span class="badge bg-danger">Recovery</span>'
                            : '<span class="badge bg-warning text-dark">Unpaid</span>';

                    const actionBtn = (e.status === 'unpaid' || e.status === 'recovery')
                        ? `<button class="btn btn-sm btn-primary" onclick="payEmi(${e.id}, '${customer.mobile}', ${transactionId})">Mark Paid</button>`
                        : '';

                    return `
                        <tr>
                            <td>${new Date(e.emi_date).toLocaleDateString('en-IN')}</td>
                            <td>${displayAmount}</td>
                            <td>${statusBadge}</td>
                            <td>${actionBtn}</td>
                        </tr>`;
                }).join('');

                html += `</tbody></table>`;

                Swal.fire({
                    title: 'EMI Details',
                    html: html,
                    showCloseButton: true,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: { popup: 'swal-wide' }
                });

            } else {
                Swal.fire('Error', data.message || 'Failed to fetch EMI details.', 'error');
            }
        });
};


    window.payEmi = function (emiId, mobile_no, transactionId) {
    fetch(`/admin/transactions/get-trans-date/${transactionId}`)
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const transDate = res.trans_date;

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Mark this EMI as paid on ${transDate}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Mark Paid'
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(`/admin/transactions/mark-emi-paid/${emiId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                mobile_no: mobile_no,
                                transaction_id: transactionId
                            })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                Swal.fire('Success', 'EMI marked as paid.', 'success').then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', res.message || 'Failed to mark EMI as paid.', 'error');
                            }
                        });
                    }
                });
            } else {
                Swal.fire('Error', res.message || 'Failed to fetch Transaction Date.', 'error');
            }
        });
};


    // Filter
    window.openFilterModal = function () {
    Swal.fire({
      title: 'Filter Transactions',
      html: `
    <form id="filterForm">
    <div class="mb-3 text-start">
    <label class="form-label">Filter By User</label>
    <select name="by" class="form-select">
    <option value="">All Users</option>
    @foreach ($users as $user)
    <option value="{{ $user }}" {{ request('by') == $user ? 'selected' : '' }}>{{ $user }}</option>
    @endforeach
    </select>
    </div>
    <div class="mb-3 text-start">
    <label class="form-label">Filter By Status</label>
    <select name="status" class="form-select">
    <option value="">All Status</option>
    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
    </select>
    </div>
    <div class="mb-3 text-start">
    <label class="form-label">Search by Mobile</label>
    <input type="text" name="mobile" placeholder="Enter Mobile Number" class="form-control" value="{{ request('mobile') }}">
    </div>
    <div class="mb-3 text-start">
    <label class="form-label">From Date</label>
    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="mb-3 text-start">
    <label class="form-label">To Date</label>
    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="d-flex justify-content-between mt-3">
    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('filterForm').reset()">Reset</button>
    </div>
    </form>`,
      confirmButtonText: 'Apply Filter',
      showCancelButton: true,
      focusConfirm: false,
      preConfirm: () => {
      const form = document.getElementById('filterForm');
      const params = new URLSearchParams(new FormData(form)).toString();
      window.location.href = `{{ route('admin.transactions.index') }}?${params}`;
      }
    });
    };



  </script>

@endsection