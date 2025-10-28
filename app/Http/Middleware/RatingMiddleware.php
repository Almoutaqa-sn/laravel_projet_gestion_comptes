<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RatingMiddleware
{
    /**
     * Limiter l'accès si l'utilisateur a atteint le Rating Limit
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Ici, 'rating_limit' peut être un champ dans la table utilisateurs
        if ($user && $user->rating >= 100) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez atteint le Rating Limit et ne pouvez plus effectuer d’actions.',
                'data' => null
            ], 403);
        }

        return $next($request);
    }
}
