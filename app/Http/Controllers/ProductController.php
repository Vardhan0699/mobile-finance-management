<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $products = Product::with('brand')->paginate(10);
    $brands = Brand::all();
    return view('product.index', compact('products', 'brands'));
  }

  public function create()
  {
    $brands = Brand::all();
    return view('product.create', compact('brands'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'product_name' => 'required',
      'brand_id' => 'required|exists:brand,id',
      'product_price' => 'required',
    ]);

    Product::create([
      'product_name' => $validated['product_name'],
      'brand_id' => $validated['brand_id'],
      'product_price' => $validated['product_price'],
    ]);

    session()->flash('success', 'Record created successfully!');

    return redirect()->route('admin.productIndex');
  }

  public function edit($id)
  {
    $product = Product::findOrFail($id);
    $brands = Brand::all();

    // $rawFileName = $product->product_image;
    // $imageUrl = null;

    // // Log::info('Raw product image value:', ['product_image' => $rawFileName]);

    // $fileName = basename(parse_url($rawFileName, PHP_URL_PATH));

    // if ($fileName) {
    //   try {
    //     $s3 = new S3Client([
    //       'version' => 'latest',
    //       'region' => env('WASABI_DEFAULT_REGION'),
    //       'endpoint' => env('WASABI_ENDPOINT'),
    //       'use_path_style_endpoint' => true,
    //       'credentials' => [
    //         'key' => env('WASABI_ACCESS_KEY_ID'),
    //         'secret' => env('WASABI_SECRET_ACCESS_KEY'),
    //       ],
    //     ]);

    //     $bucket = env('WASABI_BUCKET');
    //     $key = 'product/' . $fileName;

    //     // Log::info('Generating product pre-signed URL', ['bucket' => $bucket, 'key' => $key]);

    //     $cmd = $s3->getCommand('GetObject', [
    //       'Bucket' => $bucket,
    //       'Key' => $key,
    //     ]);

    //     $request = $s3->createPresignedRequest($cmd, '+2 hours');
    //     $presignedUrl = (string) $request->getUri();

    //     // Log::info('Generated product pre-signed URL', ['url' => $presignedUrl]);

    //   } catch (AwsException $e) {
    //     Log::error('Wasabi pre-signed URL generation failed for product image', [
    //       'message' => $e->getMessage(),
    //       'bucket' => $bucket ?? 'undefined',
    //       'key' => $key ?? 'undefined',
    //     ]);
    //   }
    // } else {
    //   Log::warning('No product_image file name found for product ID: ' . $id);
    // }

    return view('product.edit', compact('product', 'brands'));
  }

  public function update(Request $request, $id)
  {
    $product = Product::findOrFail($id);

    $validated = $request->validate([
      'product_name' => 'required',
      'brand_id' => 'required|exists:brand,id',
      'product_price' => 'required',
    ]);

    // $imagePath = $product->product_image;

    // if ($request->hasFile('product_image')) {
    // Delete old image from Wasabi
    //   if (!empty($product->product_image)) {
    //     $parsedUrl = parse_url($product->product_image);

    //     if (isset($parsedUrl['path'])) {
    //       $fullPath = ltrim($parsedUrl['path'], '/'); // e.g., your-bucket/product/oldfile.jpg
    //       $bucket = config('filesystems.disks.wasabi.bucket');
    //       $key = Str::after($fullPath, $bucket . '/'); // e.g., product/oldfile.jpg

    //      if (!empty($key)) {
    //        Storage::disk('wasabi')->delete($key);
    //      }
    //    }
    //   }

    // Upload new image to Wasabi
    //    $image = $request->file('product_image');
    //    $filename = time() . '_' . $image->getClientOriginalName();
    //     $path = $image->storeAs('product', $filename, 'wasabi');

    //     $imagePath = Storage::disk('wasabi')->url($path);
    //   }

    $product->update([
      'product_name' => $validated['product_name'],
      'brand_id' => $validated['brand_id'],
      'product_price' => $validated['product_price'],
    ]);

    return redirect()->route('admin.productIndex')->with('success', 'Product updated successfully!');
  }



  public function destroy($id)
  {
    $product = Product::findOrFail($id);

    // Delete the image from Wasabi if it exists
    // if (!empty($product->product_image)) {
    //  $parsedUrl = parse_url($product->product_image);

    //   if (isset($parsedUrl['path'])) {
    //    $fullPath = ltrim($parsedUrl['path'], '/'); // e.g., your-bucket/product/image.jpg
    //     $bucket = config('filesystems.disks.wasabi.bucket');
    //     $key = Str::after($fullPath, $bucket . '/'); // Extract actual key: product/image.jpg

    //     if (!empty($key)) {
    //       Storage::disk('wasabi')->delete($key);
    //     }
    //   }
    //  }

    // Delete the product
    $product->delete();

    return redirect()->route('admin.productIndex')->with('success', 'Product deleted successfully!');
  }

}
