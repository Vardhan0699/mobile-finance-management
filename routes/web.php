<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PincodeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Retailer\RetailerLoginController;
use App\Http\Controllers\Retailer\RetailerRegisterController;
use App\Http\Controllers\Retailer\RetailerForgotPasswordController;
use App\Http\Controllers\Retailer\RetailerResetPasswordController;
use App\Http\Controllers\RetailerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Retailer\RetailerProfileController;
use App\Http\Controllers\Retailer\RetailerCustomerController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\ResetPasswordController;
use App\Http\Controllers\AadhaarController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\EMICalculatorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RecoveryController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Artisan;

Route::get('/test-update-status', function () {
  Artisan::call('status:update-unpaid');
  return Artisan::output();
});

// Route::get('/test-update-status', function () {
//   if (app()->environment('production')) {
//     abort(403);
//   }
//   Artisan::call('status:update-unpaid');
//   return response('<pre>' . Artisan::output() . '</pre>');
// });



// Route::get('/test-mail', function () {
//    Mail::raw('Test email from Laravel', function ($message) {
//        $message->to('you@example.com')->subject('Test Mail');
//    });
//    return 'Mail sent!';
// });


// Route::get('/phpinfo', function () {
//     phpinfo();
// });


Route::prefix('admin')->name('admin.')->group(function () {
  Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
  Route::post('login/submit', [LoginController::class, 'login'])->name('login.submit');
  Route::post('logout', [LoginController::class, 'logout'])->name('logout');

  Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
  Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
  Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
  Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

  Route::middleware('adminAuth')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')
      ->middleware('check.permission:dashboard,read');


    Route::get('profile/{id}', [AdminProfileController::class, 'showProfile'])->name('profile');
    Route::post('update-profile', [AdminProfileController::class, 'profileUpdate'])->name('profileUpdate');

    Route::get('list', [DashboardController::class, 'admin_list'])->name('adminList')
      ->middleware('check.permission:staff,read');
    Route::get('add-admin', [DashboardController::class, 'create'])->name('create')
      ->middleware('check.permission:staff,write');
    Route::post('register', [DashboardController::class, 'register'])->name('register')
      ->middleware('check.permission:staff,write');
    Route::get('edit/{id}', [DashboardController::class, 'edit'])->name('edit')
      ->middleware('check.permission:staff,update');
    Route::put('update/{id}', [DashboardController::class, 'update'])->name('update')
      ->middleware('check.permission:staff,update');
    Route::delete('delete/{id}', [DashboardController::class, 'destroy'])->name(name: 'destroy')
      ->middleware('check.permission:staff,delete');
    Route::get('/export-csv', [DashboardController::class, 'exportAdminCSV'])->name('export.csv');


    Route::get('brand/index', [BrandController::class, 'index'])->name('brandIndex')
      ->middleware('check.permission:brand,read');
    Route::get('brand/create', [BrandController::class, 'create'])->name('brandCreate')
      ->middleware('check.permission:brand,write');
    Route::post('brand/store', [BrandController::class, 'store'])->name('brandStore')
      ->middleware('check.permission:brand,write');
    Route::get('brand/{id}/edit', [BrandController::class, 'edit'])->name('brandEdit')
      ->middleware('check.permission:brand,update');
    Route::put('brand/{id}/update', [BrandController::class, 'update'])->name('brandUpdate')
      ->middleware('check.permission:brand,update');
    Route::delete('brand/{id}', [BrandController::class, 'destroy'])->name('brandDestroy')
      ->middleware('check.permission:brand,delete');


    Route::get('product/index', [ProductController::class, 'index'])->name('productIndex')
      ->middleware('check.permission:product,read');
    Route::get('product/create', [ProductController::class, 'create'])->name('productCreate')
      ->middleware('check.permission:product,write');
    Route::post('product/store', [ProductController::class, 'store'])->name('productStore')
      ->middleware('check.permission:product,write');
    Route::get('product/{id}/edit', [ProductController::class, 'edit'])->name('productEdit')
      ->middleware('check.permission:product,update');
    Route::put('product/{id}', [ProductController::class, 'update'])->name('productUpdate')
      ->middleware('check.permission:product,update');
    Route::delete('product/{id}', [ProductController::class, 'destroy'])->name('productDestroy')
      ->middleware('check.permission:product,delete');

    Route::get('pincode/index', [PincodeController::class, 'index'])->name('pincodeIndex')
      ->middleware('check.permission:approved pincode,read');
    Route::get('pincode/create', [PincodeController::class, 'create'])->name('pincodeCreate')
      ->middleware('check.permission:approved pincode,write');
    Route::post('pincode/store', [PincodeController::class, 'store'])->name('pincodeStore')
      ->middleware('check.permission:approved pincode,write');
    Route::get('pincode/{id}/edit', [PincodeController::class, 'edit'])->name('pincodeEdit')
      ->middleware('check.permission:approved pincode,update');
    Route::put('pincode/{id}/update', [PincodeController::class, 'update'])->name('pincodeUpdate')
      ->middleware('check.permission:approved pincode,update');
    Route::delete('pincode/{id}', [PincodeController::class, 'destroy'])->name('pincodedDestory')
      ->middleware('check.permission:approved pincode,delete');

    Route::get('retailer/index', [RetailerController::class, 'index'])->name('retailerIndex')
      ->middleware('check.permission:retailer,read');
    Route::get('retailer/create', [RetailerController::class, 'create'])->name('retailerCreate')
      ->middleware('check.permission:retailer,write');
    Route::post('retailer/store', [RetailerController::class, 'store'])->name('retailerStore')
      ->middleware('check.permission:retailer,write');
    Route::get('retailer/{id}/edit', [RetailerController::class, 'edit'])->name('retailerEdit')
      ->middleware('check.permission:retailer,update');
    Route::put('retailer/{id}', [RetailerController::class, 'update'])->name('retailerUpdate')
      ->middleware('check.permission:retailer,update');
    Route::delete('retailer/{id}', [RetailerController::class, 'destroy'])->name('retailerDestroy')
      ->middleware('check.permission:retailer,delete');
    Route::get('/get-cities/{state_id}', [RetailerController::class, 'getCities'])
      ->middleware('check.permission:retailer,delete');

    Route::get('customer/index', [CustomerController::class, 'customer_list'])->name('customer_list')
      ->middleware('check.permission:customer,read');
    Route::get('customer/show{id}', [CustomerController::class, 'customer_show'])->name('customer_show')
      ->middleware('check.permission:customer,read');
    Route::get('customer/pendingList', [CustomerController::class, 'pendingList'])->name('pendingList')
      ->middleware('check.permission:customer,read');
    Route::post('/customer/approve/{id}', [CustomerController::class, 'approve'])->name('customer.approve')
      ->middleware('check.permission:customer,read');
    Route::get('/customers/export-excel', [AdminCustomerController::class, 'exportExcel'])->name('customers_exportExcel')
      ->middleware('check.permission:customer,read');

    Route::get('/roles', [RoleController::class, 'index'])->name('role_index')
      ->middleware('check.permission:role,read');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('role_store')
      ->middleware('check.permission:role,write');
    Route::delete('/roles/destroy{id}', [RoleController::class, 'destroy'])->name('role_destroy')
      ->middleware('check.permission:role,delete');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('role_update')
      ->middleware('check.permission:role,update');

    Route::get('/permission{role}', [PermissionController::class, 'index'])->name('permission_index')
      ->middleware('check.permission:permission,read');
    Route::post('/permissions/update/{role}', [PermissionController::class, 'update'])->name('permission_update')
      ->middleware('check.permission:permission,update');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index')
      ->middleware('check.permission:transactions,read');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store')
      ->middleware('check.permission:transactions,write');
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit')
      ->middleware('check.permission:transactions,update');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update')
      ->middleware('check.permission:transactions,update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy')
      ->middleware('check.permission:transactions,delete');
    Route::post('/transactions/{id}/pay', [TransactionController::class, 'markAsPaid'])->name('transactions.pay')
      ->middleware('check.permission:transactions,read');


    Route::get('/transactions/verify-customer', [TransactionController::class, 'verifyCustomer']);
    Route::get('/transactions/get-customer-emi-details/{loanId}', [TransactionController::class, 'getCustomerEmiDetails']);
    Route::get('/transactions/get-trans-date/{id}', [TransactionController::class, 'getTransDate']);
    Route::post('/transactions/mark-emi-paid/{id}', [TransactionController::class, 'markEmiPaid']);


    Route::get('/transactions/export-csv', [TransactionController::class, 'exportTransactionCSV'])->name('transactions.export.csv');


    Route::get('/recovery', [RecoveryController::class, 'index'])->name('recovery.index');
    Route::get('recovery/view/{id}', [RecoveryController::class, 'viewEmiDetails'])->name('recovery.view');

    Route::get('/emi-reports', [ReportsController::class, 'index'])->name('report.index');
    Route::get('/show-emi-reports/{id}', [ReportsController::class, 'show_emi_report'])->name('show_emi_report');
    Route::get('/emi_list', [ReportsController::class, 'emi_list'])->name('emi_list');
    Route::get('/reports/emi-export-csv', [ReportsController::class, 'exportEMICSV'])->name('reports.emi.export.csv');
    Route::get('/reports/emi-list/export-csv', [ReportsController::class, 'exportEMIListCSV'])->name('reports.emi_list.export.csv');
    Route::get('/retailer-report', [ReportsController::class, 'retailer_report'])->name('retailer_report');
    Route::get('reports/retailer/export/csv', [ReportsController::class, 'retailerReportExport'])->name('reports.retailer_report.export.csv');


  });

});

Route::get('/', function () {
  return view('welcome');
})->name('retailerLogin');

Route::prefix('retailer')->name('retailer.')->group(function () {

  Route::get('login', [RetailerLoginController::class, 'showLoginForm'])->name('loginForm');
  Route::post('login/submit', [RetailerLoginController::class, 'login'])->name('login');
  Route::post('logout', [RetailerLoginController::class, 'logout'])->name('logout');
  Route::get('register-form', [RetailerRegisterController::class, 'showRegistrationForm'])->name('register');
  Route::post('register', [RetailerRegisterController::class, 'register'])->name('register.submit');

  Route::get('password/forgot', [RetailerForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
  Route::post('password/email', [RetailerForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
  Route::get('password/reset/{token}', [RetailerResetPasswordController::class, 'showResetForm'])->name('password.reset');
  Route::post('password/reset', [RetailerResetPasswordController::class, 'reset'])->name('password.update');

  Route::middleware('retailer')->group(function () {

    Route::get('dashboard', [RetailerLoginController::class, 'dashboard'])->name('dashboard');

    Route::get('profile/{id}',[RetailerProfileController::class, 'showProfile'])->name('profile');
    Route::post('update-profile',[RetailerProfileController::class, 'profileUpdate'])->name('profileUpdate');

    Route::get('customer/index', [CustomerController::class, 'index'])->name('customerIndex');
    Route::get('customer/create', [CustomerController::class, 'create'])->name('customerCreate');
    Route::post('customer/store', [CustomerController::class, 'store'])->name('customerStore');    
    Route::get('customer/show{id}', [CustomerController::class, 'show'])->name('customer_data');
    Route::get('customer/edit{id}', [CustomerController::class, 'edit'])->name('customerEdit');
    Route::delete('customer/{id}', [CustomerController::class, 'destroy'])->name('customerDestory');
    Route::get('get-products/{brand_id}', [CustomerController::class, 'getProducts']);
    Route::get('get-cities/{state_id}', [CustomerController::class, 'getCities']);
    Route::get('/check-pincode', [CustomerController::class, 'checkPincode']);
    Route::post('/check-aadhar', [CustomerController::class, 'checkAadhar'])->name('check_aadhar');
    Route::get('/check-customer-mobile', [CustomerController::class, 'checkMobile']);
    Route::get('/export-customers', [CustomerController::class, 'exportCSV'])->name('export_customers');

    Route::get('/customers/export-excel', [RetailerCustomerController::class, 'exportExcel'])->name('customers_exportExcel');

    Route::get('/emi-calculator', [EMICalculatorController::class, 'index'])->name('emi.form');
    Route::post('/emi-calculator', [EMICalculatorController::class, 'calculate'])->name('emi.calculate');

    Route::post('aadhaar-verify-ajax', [CustomerController::class, 'verifyAadhaar'])->name('aadhaar.verify.ajax');

    Route::post('/verify-mobile-success', [CustomerController::class, 'verifyMobile'])->name('mobile.verify');
    Route::get('customer/search', [CustomerController::class, 'searchCustomer']);



  });

});