<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DolarApiService
{
    const USD_URL = 'https://ve.dolarapi.com/v1/dolares/oficial';
    const EUR_URL = 'https://ve.dolarapi.com/v1/euros/oficial';

    public static function getRates(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget('dolar_api_rates_v3');
        }

        return Cache::remember('dolar_api_rates_v3', 300, function () {
            $bcvUsd = 784.66;
            $bcvEur = 916.01;

            try {
                $usdResponse = Http::timeout(5)->get(self::USD_URL);
                if ($usdResponse->successful()) {
                    $usdData = $usdResponse->json();
                    if (isset($usdData['promedio'])) {
                        $bcvUsd = round((float) $usdData['promedio'], 2);
                    }
                }
            } catch (\Exception $e) {}

            try {
                $eurResponse = Http::timeout(5)->get(self::EUR_URL);
                if ($eurResponse->successful()) {
                    $eurData = $eurResponse->json();
                    if (isset($eurData['promedio'])) {
                        $bcvEur = round((float) $eurData['promedio'], 2);
                    }
                }
            } catch (\Exception $e) {}

            return [
                'bcv_usd' => $bcvUsd,
                'bcv_eur' => $bcvEur,
                'updated_at' => now()->toIso8601String(),
                'status' => 'live'
            ];
        });
    }

    public static function getBcvUsdRate(): float
    {
        return self::getRates()['bcv_usd'];
    }

    public static function getBcvEurRate(): float
    {
        return self::getRates()['bcv_eur'];
    }
}
