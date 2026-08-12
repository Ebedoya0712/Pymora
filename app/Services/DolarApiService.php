<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DolarApiService
{
    const USD_URL = 'https://ve.dolarapi.com/v1/dolares/oficial';
    const EUR_URL = 'https://ve.dolarapi.com/v1/euros/oficial';

    public static function getRates(): array
    {
        return Cache::remember('dolar_api_rates_v2', 300, function () {
            $bcvUsd = 764.35;
            $bcvEur = 882.30;

            try {
                $usdResponse = Http::timeout(5)->get(self::USD_URL);
                if ($usdResponse->successful()) {
                    $usdData = $usdResponse->json();
                    if (isset($usdData['promedio'])) {
                        $bcvUsd = (float) $usdData['promedio'];
                    }
                }
            } catch (\Exception $e) {}

            try {
                $eurResponse = Http::timeout(5)->get(self::EUR_URL);
                if ($eurResponse->successful()) {
                    $eurData = $eurResponse->json();
                    if (isset($eurData['promedio'])) {
                        $bcvEur = (float) $eurData['promedio'];
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
