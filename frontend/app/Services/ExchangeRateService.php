<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected $apiKey;
    // Primary API: ExchangeRate-API (Reliable)
    protected $primaryUrl = 'https://v6.exchangerate-api.com/v6';
    // Backup API: Open ER API (No key often needed)
    protected $backupUrl = 'https://open.er-api.com/v6/latest';

    protected $staticFallbackRates = [
        "IDR" => ["SGD" => 0.000084, "MYR" => 0.00028, "THB" => 0.0022, "PHP" => 0.0035, "USD" => 0.000062],
        "SGD" => ["IDR" => 11950, "MYR" => 3.35, "THB" => 26.5, "PHP" => 42.0, "USD" => 0.74],
        "MYR" => ["IDR" => 3570, "SGD" => 0.30, "THB" => 7.9, "PHP" => 12.5, "USD" => 0.22],
        "THB" => ["IDR" => 450, "SGD" => 0.038, "MYR" => 0.13, "PHP" => 1.58, "USD" => 0.027],
        "PHP" => ["IDR" => 285, "SGD" => 0.024, "MYR" => 0.08, "THB" => 0.63, "USD" => 0.017]
    ];

    public function __construct()
    {
        $this->apiKey = env('EXCHANGE_RATE_API_KEY');
    }

    /**
     * Convert currency using live exchange rates with 3-tier fallback.
     */
    public function convert($from, $to, $amount)
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return ['result' => $amount, 'rate' => 1.0, 'source' => 'identity'];
        }

        // 1. PRIMARY API (ExchangeRate-API)
        if ($this->apiKey) {
            try {
                $response = Http::timeout(3)->get("{$this->primaryUrl}/{$this->apiKey}/pair/{$from}/{$to}/{$amount}");
                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'result' => $data['conversion_result'],
                        'rate' => $data['conversion_rate'],
                        'source' => 'primary_api'
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Primary Exchange API failed: " . $e->getMessage());
            }
        }

        // 2. BACKUP API (Open ER API)
        try {
            $response = Http::timeout(3)->get("{$this->backupUrl}/{$from}");
            if ($response->successful()) {
                $data = $response->json();
                $rate = $data['rates'][$to] ?? null;
                if ($rate) {
                    return [
                        'result' => $amount * $rate,
                        'rate' => $rate,
                        'source' => 'backup_api'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("Backup Exchange API failed: " . $e->getMessage());
        }

        // 3. STATIC FALLBACK
        $rate = $this->staticFallbackRates[$from][$to] ?? null;
        if ($rate) {
            return [
                'result' => $amount * $rate,
                'rate' => $rate,
                'source' => 'static_fallback'
            ];
        }

        return ['error' => 'All exchange rate sources failed', 'result' => $amount, 'rate' => 1.0];
    }

    /**
     * Get latest rates for multiple currencies.
     */
    public function getLatestRates($base, $symbols)
    {
        // For the ticker, we can just use the backup API as it's free and fast
        try {
            $response = Http::get("{$this->backupUrl}/{$base}");
            if ($response->successful()) {
                $data = $response->json();
                $quotes = [];
                $symbolArray = explode(',', $symbols);
                foreach ($symbolArray as $sym) {
                    if (isset($data['rates'][$sym])) {
                        $quotes["{$base}{$sym}"] = $data['rates'][$sym];
                    }
                }
                return ['quotes' => $quotes, 'source' => 'backup_api'];
            }
        } catch (\Exception $e) {
            Log::error("Ticker rates failed: " . $e->getMessage());
        }

        return ['error' => 'Failed to fetch ticker rates'];
    }
}
