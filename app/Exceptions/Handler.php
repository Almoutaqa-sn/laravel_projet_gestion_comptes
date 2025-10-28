<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            \Log::error('Exception reportable: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        });

        $this->renderable(function (QueryException $e, $request) {
            \Log::error('Database Query Exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur de base de données',
                'message' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            \Log::warning('Model not found: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ressource non trouvée',
                'message' => 'L\'élément demandé n\'existe pas'
            ], 404);
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            \Log::warning('Route not found: ' . $request->getPathInfo());
            return response()->json([
                'error' => 'Route non trouvée',
                'message' => 'L\'endpoint demandé n\'existe pas'
            ], 404);
        });
    }
}
