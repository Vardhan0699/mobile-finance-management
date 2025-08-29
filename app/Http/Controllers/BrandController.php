<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;    
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::paginate(5);
        $adminId = session('admin_id'); 
        return view('brand.index', compact('brands','adminId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('brand.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $validatedData = $request->validate([
        'brand_name' => 'required|unique:brand,brand_name',
        'brand_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
      ]);

      $imagePath = null;

      if ($request->hasFile('brand_image')) {
        $image = $request->file('brand_image');
        $filename = time() . '_' . $image->getClientOriginalName();

        // Store in Wasabi under "brand" folder
        $path = $image->storeAs('brand', $filename, 'wasabi');

        // Get pre-signed URL from Wasabi with an expiration time of 10 minutes
        $imagePath = Storage::disk('wasabi')->temporaryUrl($path, now()->addMinutes(10));
      }

      Brand::create([
        'brand_name' => $validatedData['brand_name'],
        'brand_image' => $imagePath,
      ]);

      return redirect()->route('admin.brandIndex')->with('success', 'Brand created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

public function edit($id)
{
    $brand = Brand::findOrFail($id);
    $rawFileName = $brand->brand_image;
    $presignedUrl = null;

    Log::info('Raw brand image value:', ['brand_image' => $rawFileName]);

    // Extract only the file name in case a full URL is stored
    $fileName = basename(parse_url($rawFileName, PHP_URL_PATH));

    if ($fileName) {
        try {
            $s3 = new \Aws\S3\S3Client([
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
            $key = 'brand/' . $fileName;

            Log::info('Attempting to generate pre-signed URL', [
                'bucket' => $bucket,
                'key' => $key,
            ]);

            $cmd = $s3->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            $request = $s3->createPresignedRequest($cmd, '+2 hours');
            $presignedUrl = (string) $request->getUri();

            Log::info('Generated Wasabi pre-signed URL', ['url' => $presignedUrl]);

        } catch (\Aws\Exception\AwsException $e) {
            Log::error('Wasabi pre-signed URL generation failed', [
                'message' => $e->getMessage(),
                'bucket' => $bucket ?? 'undefined',
                'key' => $key ?? 'undefined',
            ]);
        }
    } else {
        Log::warning('No brand_image file name found for brand ID: ' . $id);
    }

    return view('brand.edit', compact('brand', 'presignedUrl'));
}




    /**
     * Update the specified resource in storage.
     */

  public function update(Request $request, $id)
  {
    $brand = Brand::findOrFail($id);

    $validatedData = $request->validate([
      'brand_name' => 'required|unique:brand,brand_name,' . $brand->id,
      'brand_image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
    ]);

    $imagePath = $brand->brand_image;

    if ($request->hasFile('brand_image')) {
        // Delete old image from Wasabi if exists
        if (!empty($brand->brand_image)) {
            $parsedUrl = parse_url($brand->brand_image);
            if (isset($parsedUrl['path'])) {
                // If URL looks like: https://s3.wasabisys.com/your-bucket-name/brand/abc.jpg
                // This will extract: brand/abc.jpg
                $wasabiPath = ltrim($parsedUrl['path'], '/');
                // Remove bucket name from path (if present)
                $bucketName = config('filesystems.disks.wasabi.bucket');
                $wasabiKey = Str::after($wasabiPath, $bucketName . '/');
                if (!empty($wasabiKey)) {
                    Storage::disk('wasabi')->delete($wasabiKey);
                }
            }
        }

        // Upload the new image to Wasabi
        $image = $request->file('brand_image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $path = $image->storeAs('brand', $filename, 'wasabi'); // saved to 'brand/' folder
        $imagePath = Storage::disk('wasabi')->url($path); // full URL saved to DB
    }

    $brand->update([
      'brand_name' => $validatedData['brand_name'],
      'brand_image' => $imagePath,
    ]);

    return redirect()->route('admin.brandIndex')->with('success', 'Brand updated successfully!');
  }


    /**
     * Remove the specified resource from storage.
     */
  
    public function destroy($id)
    {
      $brand = Brand::findOrFail($id);

      // Delete brand image from Wasabi if it exists
      if (!empty($brand->brand_image)) {
        $parsedUrl = parse_url($brand->brand_image);
        if (isset($parsedUrl['path'])) {
          // Extract path like: /your-bucket-name/brand/filename.jpg
          $fullPath = ltrim($parsedUrl['path'], '/');
          $bucketName = config('filesystems.disks.wasabi.bucket');
          $key = Str::after($fullPath, $bucketName . '/');

          if (!empty($key)) {
            Storage::disk('wasabi')->delete($key);
          }
        }
      }

      // Delete the brand record
      $brand->delete();

      return redirect()->route('admin.brandIndex')->with('success', 'Brand deleted successfully!');
    }

}
