<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log; // Import Log facade

class Exchange
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('EXCHANGE_RATE_API_KEY');
    }


    public function getExchangeRate($fromCurrency, $toCurrency)
    {
        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";
        
        // Try to get the rate from cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
    
        try {
            $response = Http::get("https://v6.exchangerate-api.com/v6/d0d7188d91998127e874bde2/pair/{$fromCurrency}/{$toCurrency}");
            
            if ($response->successful()) {
                $data = $response->json();
                // Cache the result for 1 hour (3600 seconds)
                Cache::put($cacheKey, $data, 3600);
                return $data;
            } else {
                $errorMessage = $response->json()['error-type'] ?? 'Unknown error';
                Log::error('Exchange rate API error', [
                    'status' => $response->status(),
                    'error' => $errorMessage,
                    'response' => $response->body()
                ]);
                throw new \Exception("Error fetching exchange rate: {$errorMessage}");
            }
        } catch (\Exception $e) {
            Log::error('Exception in getExchangeRate', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception("Error fetching exchange rate: " . $e->getMessage());
        }
    }
}
