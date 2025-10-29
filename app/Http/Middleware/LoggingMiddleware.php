<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Log de la requête entrante
        Log::info('API Request Started', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'host' => $request->getHost(),
            'operation' => $this->getOperationName($request),
            'timestamp' => now()->toISOString()
        ]);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // en millisecondes

        // Log de la réponse
        Log::info('API Request Completed', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'operation' => $this->getOperationName($request),
            'resource' => $this->getResourceName($request),
            'timestamp' => now()->toISOString()
        ]);

        return $response;
    }

    /**
     * Détermine le nom de l'opération
     */
    private function getOperationName(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        if (str_contains($path, '/comptes')) {
            switch ($method) {
                case 'GET':
                    return str_contains($path, '/comptes/') ? 'CONSULTATION_COMPTE' : 'LISTE_COMPTES';
                case 'POST':
                    return 'CREATION_COMPTE';
                case 'PUT':
                case 'PATCH':
                    return 'MODIFICATION_COMPTE';
                case 'DELETE':
                    return 'SUPPRESSION_COMPTE';
            }
        }

        return strtoupper($method) . '_' . str_replace('/', '_', $path);
    }

    /**
     * Détermine le nom de la ressource
     */
    private function getResourceName(Request $request): string
    {
        $path = $request->path();

        if (str_contains($path, '/comptes')) {
            if (preg_match('/\/comptes\/([^\/]+)/', $path, $matches)) {
                return 'COMPTE_' . $matches[1];
            }
            return 'COMPTES';
        }

        return strtoupper(str_replace('/', '_', $path));
    }
}
