<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyDetectionService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('BACKEND_API_URL', 'http://127.0.0.1:5000');
    }

    /**
     * Test connection to the Python backend.
     */
    public function testConnection()
    {
        try {
            $response = Http::get("{$this->baseUrl}/test");
            return $response->json();
        } catch (\Exception $e) {
            Log::error("Backend connection failed: " . $e->getMessage());
            return ['error' => 'Could not connect to backend'];
        }
    }

    /**
     * Send an image to the Python backend for currency detection.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return array
     */
    public function predict($image)
    {
        try {
            $response = Http::attach(
                'image', 
                file_get_contents($image->getRealPath()), 
                $image->getClientOriginalName()
            )->post("{$this->baseUrl}/predict");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => 'API Request failed',
                'status' => $response->status(),
                'details' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error("Prediction request failed: " . $e->getMessage());
            return ['error' => 'System error during prediction'];
        }
    }
}
