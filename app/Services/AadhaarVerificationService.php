<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AadhaarVerificationService
{
    public function verify($aadhaarNumber)
    {
        try {
            $url = config('services.aadhaar.url');

            $payload = [
                'aadhaarNumber' => $aadhaarNumber,
            ];

            $headers = [
                'clientId' => config('services.aadhaar.client_id'),
                'clientSecret' => config('services.aadhaar.client_secret'),
                'Content-Type' => 'application/json',
            ];

         //   Log::info('📤 Sending Aadhaar API request', [
          //      'url' => $url,
         //       'payload' => $payload,
          //      'headers' => $headers
          //  ]);

            $response = Http::withHeaders($headers)->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            } else {
            //    Log::error('❌ Aadhaar API error: ' . $response->body());

                return [
                    'responseCode' => 'ERROR',
                    'responseMessage' => 'Failed to verify Aadhaar number.',
                ];
            }
        } catch (\Throwable $e) {
         //   \Log::error('❌ HTTP Exception: ' . $e->getMessage());

            return [
                'responseCode' => 'ERROR',
                'responseMessage' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }
}
