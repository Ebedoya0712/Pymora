<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DolarApiService
{
    const API_URL = 'https://ve.dolarapi.com/v1/dolares';

    public static function getRates(): array
    {
        return Cache::remember('dolar_api_rates', 300, function () {
            try {
                $response = Http::timeout(5)->get(self::API_URL);
                if ($response->successful()) {
                    $data = $response->json();
                    $bcv = collect($data)->firstWhere('fuente', 'oficial')['promedio'] ?? 52.40;
                    $paralelo = collect($data)->firstWhere('fuente', 'paralelo')['promedio'] ?? 54.10;

                    return [
                        'bcv' => (float) $bcv,
                        'paralelo' => (float) $paralelo,
                        'updated_at' => now()->toIso8601String(),
                        'status' => 'live'
                    ];
                }
            } catch (\Exception $e) {
                // Fallback rates if external API is unreachable
            }

            return [
                'bcv' => 52.40,
                'paralelo' => 54.10,
                'updated_at' => now()->toIso8601String(),
                'status' => 'fallback'
            ];
        });
    }

    public static function getBcvRate(): float
    {
        return self::getRates()['bcv'];
    }

    public static function getParaleloRate(): float
    {
        return self::getRates()['paralelo'];
    }
}
