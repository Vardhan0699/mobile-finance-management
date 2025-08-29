<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Aws\Exception\AwsException;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\States;
use App\Models\Cities;
use App\Models\Pincode;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use App\Services\AadhaarVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class CustomerController extends Controller
{

  protected $aadhaarService;

  public function __construct(AadhaarVerificationService $aadhaarService)
  {
    $this->aadhaarService = $aadhaarService;
  }

  public function verifyAadhaar(Request $request)
  {
    try {
      $request->validate([
        'aadhaar_number' => 'required|digits:12',
      ]);

      $aadhaarNumber = $request->aadhaar_number;

      //   \Log::info("Verifying Aadhaar: " . $aadhaarNumber);

      $response = $this->aadhaarService->verify($aadhaarNumber);

      //  \Log::info("Aadhaar response", $response);

      if (isset($response['responseCode']) && $response['responseCode'] === 'SRC001') {

        return response()->json([
          'status' => 'success',
          'data' => $response['data'],
        ]);
      }

      return response()->json([
        'status' => 'error',
        'message' => $response['responseMessage'] ?? 'Verification failed.',
      ]);
    } catch (\Throwable $e) {
      //   \Log::error('❌ Aadhaar verification exception: ' . $e->getMessage());

      return response()->json([
        'status' => 'error',
        'message' => 'Something went wrong on the server.',
      ], 500);
    }
  }

  public function verifyMobile(Request $request)
  {
    $request->validate([
      'mobile' => 'required|string|max:15'
    ]);

    $mobile = str_replace('+91', '', $request->mobile);

    $customer = Customer::where('mobile', $mobile)->first();

    if ($customer) {
      $customer->mobile_verified = 1;
      $customer->save();
      return response()->json(['status' => 'success', 'message' => 'Mobile verified and updated.']);
    } else {
      return response()->json(['status' => 'not_found', 'message' => 'Customer not found.']);
    }
  }

  public function index()
  {
    $retailerId = session('retailer_id');
    $customers = Customer::with(['loans.brand', 'loans.product'])
      ->where('retailer_id', $retailerId)
      ->paginate(10);

    return view('customer.index', compact('customers'));
  }


  public function create()
  {
    $brands = Brand::all();
    $products = Product::all();
    $states = States::all();
    return view('customer.create', compact('brands', 'products', 'states'));
  }

  private function generateUniqueLoanId(): string
  {
    $numbers = '0123456789';
    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $maxTries = 10; // Optional safety limit
    $tries = 0;

    do {
      $tries++;
      if ($tries > $maxTries) {
        throw new \Exception("Unable to generate a unique loan ID after $maxTries attempts.");
      }


      // Start with 'vardan' (6 chars)
      $prefix = 'VARDAN';

      // Generate 1 random number
      $number = $numbers[random_int(0, strlen($numbers) - 1)];

      // Generate remaining 4 random uppercase letters or numbers
      $remainingLength = 12 - strlen($prefix) - strlen($number); // 4
      $remainingChars = $letters . $numbers;
      $remaining = '';
      for ($i = 0; $i < $remainingLength; $i++) {
        $remaining .= $remainingChars[random_int(0, strlen($remainingChars) - 1)];
      }

      // Shuffle the parts after prefix to randomize the position of special/number/letters
      $suffixArray = str_split($number . $remaining);
      shuffle($suffixArray);
      $suffix = implode('', $suffixArray);

      // Final loan ID
      $loanId = $prefix . $suffix;

    } while (Loan::where('loanID', $loanId)->exists());

    return $loanId;
  }


//   public function store(Request $request)
//   {
//     $request->merge([
//       'aadhaar_number' => preg_replace('/\s+/', '', $request->aadhaar_number),
//     ]);

//     $validated = $request->validate([
//       'retailer_id' => 'required|exists:retailer,id',
//       'is_existing_customer' => 'required|in:0,1',

//       // Customer fields only required for new customer
//       'customer_firstname' => 'required_if:is_existing_customer,0|string|max:100',
//       'customer_lastname' => 'required_if:is_existing_customer,0|string|max:100',
//       'date_of_birth' => [
//         'required_if:is_existing_customer,0',
//         'date',
//         function ($attribute, $value, $fail) use ($request) {
//           if ($request->is_existing_customer == 0 && \Carbon\Carbon::parse($value)->age < 18) {
//             $fail('The customer must be at least 18 years old.');
//           }
//         }
//       ],
//       'father_name' => 'required_if:is_existing_customer,0|string|max:100',
//       'address1' => 'required_if:is_existing_customer,0|string|max:255',
//       'address2' => 'nullable|string|max:255',
//       'nearby' => 'nullable|string|max:255',
//       'post' => 'nullable|string|max:255',
//       'mohalla' => 'nullable|string|max:255',
//       'village' => 'nullable|string|max:255',
//       'state_id' => 'required_if:is_existing_customer,0|exists:states,id',
//       'city_id' => 'required_if:is_existing_customer,0|exists:cities,id',
//       'pincode' => ['required_if:is_existing_customer,0', 'digits:6'],
//       'aadhaar_number' => ['required', 'string', 'regex:/^\d{12}$/', 'max:255'],

//       // Loan fields
//       'sell_price' => 'required|numeric',
//       'disburse_amount' => 'nullable|numeric',
//       'brand_id' => 'required|exists:brand,id',
//       'product_id' => 'required|exists:product,id',
//       'imei1' => 'required|string|max:15',
//       'imei2' => 'required|string|max:15',
//       'downpayment' => 'required|numeric',
//       'downpayment_pending' => 'nullable|numeric',
//       'emi' => 'nullable|numeric',
//       'months' => 'required|string',
//       'total_interest' => 'nullable|numeric',
//       'total_payment' => 'nullable|numeric',

//       // Customer Mobile
//       'mobile' => 'required_if:is_existing_customer,0|string|max:15',
//       'alternate_mobile' => 'required_if:is_existing_customer,0|string|max:15',

//       // Images
//       'selfie' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
//       'adharcard_front' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
//       'adharcard_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
//     ]);

//     try {
//       \DB::beginTransaction();

//       // Step 1: Find or Create Customer
//       if ($validated['is_existing_customer'] == 0) {
//         $customer = Customer::where('aadhaar_number', $validated['aadhaar_number'])->first();

//         if (!$customer) {
//           return back()->withErrors(['aadhaar_number' => 'Customer not found with this Aadhaar number.'])->withInput();
//         }
//       } else {
//         if (Customer::where('aadhaar_number', $validated['aadhaar_number'])->exists()) {
//           return back()->withErrors(['aadhaar_number' => 'A customer with this Aadhaar number already exists. Please select "Existing Customer".'])->withInput();
//         }

//         $pincodeExists = Pincode::where('pincode', $validated['pincode'])->exists();
//         if (!$pincodeExists) {
//           return back()->withErrors(['pincode' => 'This pincode is not serviceable.'])->withInput();
//         }

//         $customer = Customer::create([
//           'retailer_id' => $validated['retailer_id'],
//           'customer_firstname' => $validated['customer_firstname'],
//           'customer_lastname' => $validated['customer_lastname'],
//           'date_of_birth' => $validated['date_of_birth'],
//           'father_name' => $validated['father_name'],
//           'address1' => $validated['address1'],
//           'address2' => $validated['address2'],
//           'nearby' => $validated['nearby'],
//           'post' => $validated['post'],
//           'mohalla' => $validated['mohalla'],
//           'village' => $validated['village'],
//           'state_id' => $validated['state_id'],
//           'city_id' => $validated['city_id'],
//           'pincode' => $validated['pincode'],
//           'aadhaar_number' => $validated['aadhaar_number'],
//           'mobile' => $validated['mobile'],
//           'alternate_mobile' => $validated['alternate_mobile'],
//         ]);

//         // Handle Image Uploads
//         $uploads = [];
//         $folder = "customer/{$customer->id}_" . Str::slug($customer->customer_firstname);
//         $wasabiBaseUrl = 'https://s3.wasabisys.com/retailer-bucket/';

//         foreach (['selfie', 'adharcard_front', 'adharcard_back'] as $field) {
//           if ($request->hasFile($field)) {
//             $file = $request->file($field);
//             $filename = time() . '_' . Str::slug($file->getClientOriginalName());
//             $filePath = "$folder/$filename";

//             Storage::disk('wasabi')->put($filePath, file_get_contents($file), 'public');
//             $uploads[$field] = $wasabiBaseUrl . $filePath;
//           }
//         }

//         $customer->update($uploads);
//       }

//       // Step 2: Optional IMEI Duplication Check
//       $existingLoan = Loan::where('imei1', $validated['imei1'])
//         ->orWhere('imei2', $validated['imei2'])
//         ->first();

//       if ($existingLoan) {
//         return back()->withErrors(['imei1' => 'A loan already exists for this IMEI.'])->withInput();
//       }

//       // Step 3: Create Loan
//       $loanId = $this->generateUniqueLoanId();

//       $loan = Loan::create([
//         'customer_id' => $customer->id,
//         'retailer_id' => $validated['retailer_id'],
//         'loanID' => $loanId,
//         'brand_id' => $validated['brand_id'],
//         'product_id' => $validated['product_id'],
//         'imei1' => $validated['imei1'],
//         'imei2' => $validated['imei2'],
//         'sell_price' => $validated['sell_price'],
//         'disburse_amount' => $validated['disburse_amount'],
//         'downpayment' => $validated['downpayment'],
//         'emi' => $validated['emi'],
//         'months' => $validated['months'],
//         'total_interest' => $validated['total_interest'],
//         'total_payment' => $validated['total_payment'],
//       ]);

//       // Step 4: Create EMI Schedule
//       $emiMonths = (int) $validated['months'];
//       $emiAmount = $validated['emi'];
//       $firstEmiDate = \Carbon\Carbon::now()->addDays(30);

//       $emiScheduleData = [];

//       for ($i = 0; $i < $emiMonths; $i++) {
//         $emiScheduleData[] = [
//           'loan_id' => $loanId,
//           'customer_id' => $customer->id,
//           'vendor_id' => $validated['retailer_id'],
//           'emi_no' => $i + 1,
//           'emi_date' => $firstEmiDate->copy()->addMonths($i),
//           'amount' => $emiAmount,
//           'status' => 'unpaid',
//           'created_at' => now(),
//           'updated_at' => now(),
//         ];
//       }

//       \DB::table('emi_schedule')->insert($emiScheduleData);

//       \DB::commit();

//       return redirect()->route('retailer.customerIndex')->with('success', 'Loan created successfully!');

//     } catch (\Exception $e) {
//       \DB::rollBack();
//       return back()->withErrors(['error' => 'Failed to save data: ' . $e->getMessage()])->withInput();
//     }
//   }

public function store(Request $request)
{
    $request->merge([
        'aadhaar_number' => preg_replace('/\s+/', '', $request->aadhaar_number),
    ]);

    $validated = $request->validate([
        'retailer_id' => 'required|exists:retailer,id',

        // New customer fields
        'customer_firstname' => 'required_if:is_existing_customer,0|string|max:100',
        'customer_lastname' => 'required_if:is_existing_customer,0|string|max:100',
        'date_of_birth' => [
            'required_if:is_existing_customer,0',
            'date',
            function ($attribute, $value, $fail) {
                if (\Carbon\Carbon::parse($value)->age < 18) {
                    $fail('The customer must be at least 18 years old.');
                }
            }
        ],
        'father_name' => 'required_if:is_existing_customer,0|string|max:100',
        'address1' => 'required_if:is_existing_customer,0|string|max:255',
        'address2' => 'nullable|string|max:255',
        'nearby' => 'nullable|string|max:255',
        'post' => 'nullable|string|max:255',
        'mohalla' => 'nullable|string|max:255',
        'village' => 'nullable|string|max:255',
        'state_id' => 'required_if:is_existing_customer,0|exists:states,id',
        'city_id' => 'required_if:is_existing_customer,0|exists:cities,id',
        'pincode' => ['required_if:is_existing_customer,0', 'digits:6'],
        'aadhaar_number' => ['required', 'string', 'regex:/^\d{12}$/', 'max:255'],
        'mobile' => 'required|string|max:15',
        'alternate_mobile' => 'required_if:is_existing_customer,0|string|max:15',

        // Loan
        'sell_price' => 'required|numeric',
        'disburse_amount' => 'nullable|numeric',
        'brand_id' => 'required|exists:brand,id',
        'product_id' => 'required|exists:product,id',
        'imei1' => 'required|string|max:15',
        'imei2' => 'required|string|max:15',
        'downpayment' => 'required|numeric',
        'downpayment_pending' => 'nullable|numeric',
        'emi' => 'nullable|numeric',
        'months' => 'required|string',
        'total_interest' => 'nullable|numeric',
        'total_payment' => 'nullable|numeric',

        // Images
        'selfie' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        'adharcard_front' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        'adharcard_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
    ]);

    try {
        \DB::beginTransaction();

        // Step 1: Search customer by Aadhaar + Mobile
        $customer = Customer::where('aadhaar_number', $validated['aadhaar_number'])
            ->where('mobile', $validated['mobile'])
            ->first();

        // Step 2: If customer not found, create new one
        if (!$customer) {
            if (!Pincode::where('pincode', $validated['pincode'])->exists()) {
                return back()->withErrors(['pincode' => 'This pincode is not serviceable.'])->withInput();
            }

            $customer = Customer::create([
                'retailer_id' => $validated['retailer_id'],
                'customer_firstname' => $validated['customer_firstname'],
                'customer_lastname' => $validated['customer_lastname'],
                'date_of_birth' => $validated['date_of_birth'],
                'father_name' => $validated['father_name'],
                'address1' => $validated['address1'],
                'address2' => $validated['address2'],
                'nearby' => $validated['nearby'],
                'post' => $validated['post'],
                'mohalla' => $validated['mohalla'],
                'village' => $validated['village'],
                'state_id' => $validated['state_id'],
                'city_id' => $validated['city_id'],
                'pincode' => $validated['pincode'],
                'aadhaar_number' => $validated['aadhaar_number'],
                'mobile' => $validated['mobile'],
                'alternate_mobile' => $validated['alternate_mobile'],
            ]);
        }

        // Step 3: Upload photos (for new or existing customer)
        $uploads = [];
        $folder = "customer/{$customer->id}_" . Str::slug($customer->customer_firstname);
        $wasabiBaseUrl = 'https://s3.wasabisys.com/retailer-bucket/';

        foreach (['selfie', 'adharcard_front', 'adharcard_back'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . Str::slug($file->getClientOriginalName());
                $filePath = "$folder/$filename";

                Storage::disk('wasabi')->put($filePath, file_get_contents($file), 'public');
                $uploads[$field] = $wasabiBaseUrl . $filePath;
            }
        }

        if (!empty($uploads)) {
            $customer->update($uploads);
        }

        // Step 4: Prevent IMEI duplication
        $existingLoan = Loan::where('imei1', $validated['imei1'])
            ->orWhere('imei2', $validated['imei2'])
            ->first();

        if ($existingLoan) {
            return back()->withErrors(['imei1' => 'A loan already exists for this IMEI.'])->withInput();
        }

        // Step 5: Create loan
        $loanId = $this->generateUniqueLoanId();

        $loan = Loan::create([
            'customer_id' => $customer->id,
            'retailer_id' => $validated['retailer_id'],
            'loanID' => $loanId,
            'brand_id' => $validated['brand_id'],
            'product_id' => $validated['product_id'],
            'imei1' => $validated['imei1'],
            'imei2' => $validated['imei2'],
            'sell_price' => $validated['sell_price'],
            'disburse_amount' => $validated['disburse_amount'],
            'downpayment' => $validated['downpayment'],
            'emi' => $validated['emi'],
            'months' => $validated['months'],
            'total_interest' => $validated['total_interest'],
            'total_payment' => $validated['total_payment'],
        ]);

        // Step 6: Create EMI schedule
        $emiMonths = (int) $validated['months'];
        $emiAmount = $validated['emi'];
        $firstEmiDate = \Carbon\Carbon::now()->addDays(30);

        $emiScheduleData = [];

        for ($i = 0; $i < $emiMonths; $i++) {
            $emiScheduleData[] = [
                'loan_id' => $loanId,
                'customer_id' => $customer->id,
                'vendor_id' => $validated['retailer_id'],
                'emi_no' => $i + 1,
                'emi_date' => $firstEmiDate->copy()->addMonths($i),
                'amount' => $emiAmount,
                'status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \DB::table('emi_schedule')->insert($emiScheduleData);

        \DB::commit();

        return redirect()->route('retailer.customerIndex')->with('success', 'Loan created successfully!');
    } catch (\Exception $e) {
        \DB::rollBack();
        return back()->withErrors(['error' => 'Failed to save data: ' . $e->getMessage()])->withInput();
    }
}



  // CustomerController.php
  public function searchCustomer(Request $request)
  {
    $type = $request->type;
    $value = $request->value;

    if ($type === 'aadhaar') {
      $customer = Customer::where('aadhaar_number', $value)->first();
    } elseif ($type === 'mobile') {
      $customer = Customer::where('mobile', $value)->first();
    } else {
      return response()->json(['success' => false, 'message' => 'Invalid search type.']);
    }

    if ($customer) {
      $activeLoans = Loan::where('customer_id', $customer->id)->count();
      return response()->json([
        'success' => true,
        'customer' => $customer,
        'active_loans' => $activeLoans
      ]);
    }

    return response()->json(['success' => false, 'message' => 'Customer not found.']);
  }


  public function show(Request $request, $id)
  {
    $customer = Customer::with(['state', 'city'])->findOrFail($id);
    $states = States::all();
    $cities = Cities::all();
    $loans = Loan::with(['brand', 'product', 'emiSchedules'])
      ->where('customer_id', $id)
      ->get();

      // dd($loans->toArray());

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

    return view('customer.show', compact('states', 'customer', 'cities', 'imageUrls', 'loans'));
  }

  public function edit($id)
  {
    $customers = Customer::findOrFail($id);
    $states = States::all();
    $cities = Cities::all();
    $brands = Brand::all();
    $products = Product::all();

    $imageUrls = [];

    // Set up the Wasabi S3 client
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

    // Generate pre-signed URLs for the images
    foreach (['selfie', 'adharcard_front', 'adharcard_back'] as $field) {
      $rawPath = $customers->$field;

      if (!empty($rawPath)) {
        try {
          // Extract path from full URL or get it directly if it's just a key
          $parsedPath = parse_url($rawPath, PHP_URL_PATH);
          $key = ltrim($parsedPath, '/');

          // Remove bucket name if included in the path
          $bucket = env('WASABI_BUCKET');
          if (str_starts_with($key, "{$bucket}/")) {
            $key = substr($key, strlen("{$bucket}/"));
          }

          $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key,
          ]);

          $request = $s3->createPresignedRequest($cmd, '+2 hours');
          $imageUrls[$field] = (string) $request->getUri();
        } catch (AwsException $e) {
          //     \Log::error("Wasabi error for {$field}: " . $e->getMessage());
          $imageUrls[$field] = null;
        }
      } else {
        $imageUrls[$field] = null;
      }
    }

    return view('customer.edit', compact('states', 'brands', 'customers', 'cities', 'products', 'imageUrls'));
  }

  public function destroy($id)
  {
    $customer = Customer::findOrFail($id);

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

    $imageFields = ['selfie', 'adharcard_front', 'adharcard_back'];

    foreach ($imageFields as $field) {
      $path = $customer->$field;

      if ($path) {
        $key = ltrim(parse_url($path, PHP_URL_PATH), '/');
        $bucket = env('WASABI_BUCKET');

        // Remove bucket name if mistakenly included
        if (str_starts_with($key, $bucket . '/')) {
          $key = substr($key, strlen($bucket) + 1);
        }

        try {
          $s3->deleteObject([
            'Bucket' => $bucket,
            'Key' => $key,
          ]);
          // \Log::info("Deleted from Wasabi: $key");
        } catch (\Aws\Exception\AwsException $e) {
          //  \Log::error("Failed to delete image from Wasabi: {$key}. Error: " . $e->getAwsErrorMessage());
        }
      }
    }

    $customer->emiSchedules()->delete();

    $customer->delete();

    return redirect()->route('retailer.customerIndex')->with('success', 'Customer deleted successfully.');
  }



  public function getProducts($brand_id)
  {
    $products = Product::where('brand_id', $brand_id)
      ->select('id', 'product_name as name', 'product_price')
      ->get();

    return response()->json($products);
  }

  public function getCities($state_id)
  {
    $cities = Cities::where('state_id', $state_id)->get();

    return response()->json($cities);
  }

  public function customer_list()
  {
    $customers = Customer::with(['retailer', 'brand', 'product'])->paginate(10);
    return view('admin.customer-list', compact('customers'));
  }


  public function checkPincode(Request $request)
  {
    $pincode = $request->input('pincode');

    if (!is_numeric($pincode) || strlen($pincode) != 6) {
      return response()->json([
        'approved' => false,
        'message' => 'Invalid pincode format.'
      ]);
    }

    $exists = Pincode::where('pincode', $pincode)->exists();

    return response()->json([
      'approved' => $exists
    ]);
  }

  public function checkAadhar(Request $request)
  {
    $aadharNumber = $request->input('aadhaar_number');
    $exists = Customer::where('aadhaar_number', $aadharNumber)->exists();

    return response()->json(['exists' => $exists]);
  }


  public function checkMobile(Request $request)
  {
    $mobile = $request->query('mobile');
    $exists = Customer::where('mobile', $mobile)->exists();

    return response()->json(['exists' => $exists]);
  }

public function exportCSV()
{
    $customers = Customer::with(['loans.brand', 'loans.product', 'loans.emiSchedules'])->get();

    $filename = "customers_loans_emi_" . now()->format('Ymd_His') . ".csv";

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename={$filename}",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $callback = function () use ($customers) {
        $handle = fopen('php://output', 'w');

        // Header row
        fputcsv($handle, [
            'Customer ID',
            'Customer Name',
            'Mobile',
            'Address',
            'Loan ID',
            'Brand',
            'Product',
            'IMEI1',
            'IMEI2',
            'Sell Price',
            'Disburse Amount',
            'Downpayment',
            'EMI Amount',
            'Loan Months',
            'Total Interest',
            'Total Payment',
            'EMI No',
            'EMI Date',
            'EMI Amount',
            'Late Fees',
            'EMI Status',
        ]);

        foreach ($customers as $customer) {
            foreach ($customer->loans as $loan) {
                foreach ($loan->emiSchedules as $emi) {
                    fputcsv($handle, [
                        $customer->id,
                        $customer->customer_firstname . ' ' . $customer->customer_lastname,
                        $customer->mobile,
                        $customer->address1 . ' ' . $customer->address2,
                        $loan->loanID,
                        $loan->brand->brand_name ?? '',
                        $loan->product->product_name ?? '',
                        $loan->imei1,
                        $loan->imei2,
                        $loan->sell_price,
                        $loan->disburse_amount,
                        $loan->downpayment,
                        $loan->emi,
                        $loan->months,
                        $loan->total_interest,
                        $loan->total_payment,
                        $emi->emi_no,
                        $emi->emi_date,
                        $emi->amount,
                        $emi->late_fees,
                        $emi->status,
                    ]);
                }
            }
        }

        fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
}



  public function customer_show($id)
  {
    $states = States::all();
    $cities = Cities::all();
    $brands = Brand::all();
    $products = Product::all();
    $customers = Customer::findOrFail($id);

    $loans = Loan::with(['brand', 'product', 'emiSchedules'])
        ->where('customer_id', $customers->id)
        ->orderBy('id', 'desc')
        ->get();

    $emiSchedules = \DB::table('emi_schedule')->where('customer_id', $customers->id)->orderBy('emi_no')->get();

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

    $bucket = env('WASABI_BUCKET');

    foreach (['selfie', 'adharcard_front', 'adharcard_back'] as $imageField) {
      $rawPath = $customers->$imageField;

      if (!empty($rawPath)) {
        try {
          // Handle both full URLs and plain keys
          $parsedPath = parse_url($rawPath, PHP_URL_PATH);
          $key = ltrim($parsedPath, '/');

          // Remove bucket prefix if present
          if (str_starts_with($key, "{$bucket}/")) {
            $key = substr($key, strlen("{$bucket}/"));
          }

          $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key,
          ]);

          $request = $s3->createPresignedRequest($cmd, '+2 hours');
          $imageUrls[$imageField] = (string) $request->getUri();
        } catch (AwsException $e) {
          //   \Log::error("Wasabi error for {$imageField}: " . $e->getAwsErrorMessage());
          $imageUrls[$imageField] = null;
        }
      } else {
        $imageUrls[$imageField] = null;
      }
    }

    return view('admin.customer-show', compact('states', 'brands', 'customers', 'cities', 'products', 'imageUrls', 'emiSchedules', 'loans'));
  }

  public function pendingList()
  {
    $customers = Customer::select(
      'customer.*',
      'retailer.firstname as retailer_firstname',
      'retailer.lastname as retailer_lastname'
    )
      ->join('retailer', 'customer.retailer_id', '=', 'retailer.id')
      ->paginate(10);

    return view('admin.pending_list', compact('customers'));
  }

  public function approve($id)
  {
    $customer = Customer::findOrFail($id);
    $customer->status = 1;
    $customer->save();

    return redirect()->back()->with('success', 'Customer approved successfully.');
  }


}
