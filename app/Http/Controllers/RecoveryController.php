<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmiSchedule;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Pincode;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class RecoveryController extends Controller
{
  public function index(Request $request)
  {
    $admin = Auth::guard('admin')->user();

    // $query = EmiSchedule::with('customer')->where('status', 'recovery');

    $query = EmiSchedule::select('emi_schedule.*', 'admin.firstname as staff_firstname', 'admin.lastname as staff_lastname')
    ->leftJoin('recovery', 'emi_schedule.id', '=', 'recovery.emi_schedule_id')
    ->leftJoin('admin', 'recovery.staff_id', '=', 'admin.id')
    ->with('customer')
    ->where(function ($q) {
        $q->where('emi_schedule.status', 'recovery')
          ->orWhere('emi_schedule.was_recovery', true);
    })
    ->whereDate('emi_schedule.emi_date', '<=', now()->subDays(3)->toDateString());

    // Only filter if not super_admin
    if ($admin->role_id != 1) {
      // Step 1: Convert JSON zipcode IDs to array
      $zipcodeIds = is_string($admin->zipcode)
        ? json_decode($admin->zipcode, true)
        : (is_array($admin->zipcode) ? $admin->zipcode : []);

      $zipcodeIds = array_filter(array_map('intval', $zipcodeIds));

      if (!empty($zipcodeIds)) {
        // Step 2: Get corresponding pincodes from approve_pincode
        $pincodes = Pincode::whereIn('id', $zipcodeIds)->pluck('pincode')->toArray();

        // Step 3: Filter by customers whose pincode matches
        $query->whereHas('customer', function ($q) use ($pincodes) {
          $q->whereIn('pincode', $pincodes);
        });
      } else {
        // No zipcode IDs provided
        $query->whereRaw('1 = 0');
      }
    }

    // Filters
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('from_date')) {
      $query->whereDate('emi_date', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
      $query->whereDate('emi_date', '<=', $request->to_date);
    }

    $recoveryTransactions = $query->orderBy('emi_date', 'desc')->paginate(10);

    return view('recovery.index', compact('recoveryTransactions'));
  }

  public function viewEmiDetails($id)
  {
    $emi = EmiSchedule::with('customer')->findOrFail($id);
    $customer = $emi->customer;

    $emiSchedules = EmiSchedule::where('customer_id', $customer->id)->orderBy('emi_no')->get();

    $imageUrls = [];

    // Initialize Wasabi S3 client
    $s3 = new S3Client([
      'version' => 'latest',
      'region' => env('WASABI_DEFAULT_REGION'),
      'endpoint' => env('WASABI_ENDPOINT'),
      'use_path_style_endpoint' => true,
      'credentials' => [
        'key' => env('WASABI_ACCESS_KEY_ID'),
        'secret' => env('WASABI_SECRET_ACCESS_KEY'),
      ],
    ]);

    foreach (['selfie', 'adharcard_front', 'adharcard_back'] as $imageField) {
      $rawPath = $customer->$imageField;

      if (!empty($rawPath)) {
        try {
          // Extract key from URL or path
          $parsedPath = parse_url($rawPath, PHP_URL_PATH);
          $key = ltrim($parsedPath, '/');

          $bucket = env('WASABI_BUCKET');
          if (str_starts_with($key, "{$bucket}/")) {
            $key = substr($key, strlen("{$bucket}/"));
          }

          $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key,
          ]);

          $presignedRequest = $s3->createPresignedRequest($cmd, '+12 hours');
          $imageUrls[$imageField] = (string) $presignedRequest->getUri();
        } catch (AwsException $e) {
          $imageUrls[$imageField] = null;
        }
      } else {
        $imageUrls[$imageField] = null;
      }
    }

    return view('recovery.view_emi_details', compact('emi', 'customer', 'emiSchedules', 'imageUrls'));
  }


}
