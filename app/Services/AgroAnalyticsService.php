<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AgroAnalyticsService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.agro_analytics.url', env('AGRO_ANALYTICS_URL', 'http://python.analytics:8000'));
    }

    /**
     * Revisa el estado de salud del motor de Python
     */
    public function checkHealth(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/health");
            return $response->json() ?? ['status' => 'error'];
        } catch (Exception $e) {
            Log::error('Error al comunicar con AgroAnalytics Python: ' . $e->getMessage());
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    /**
     * Ejemplo: Obtener métricas consolidadas de mermas
     */
    public function obtenerAnaliticaMermas(array $filtros = []): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/api/v1/analytics/mermas", $filtros);
            
            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'No se pudo procesar la analítica'];
        } catch (Exception $e) {
            Log::error('Error consultando analítica de mermas: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}