<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CompteService;
use App\Http\Resources\CompteResource;
use App\Traits\ApiResponse;
use App\Models\Compte;
use OpenApi\Annotations as OA;

/**
 * @OA\Server(
 *     url="https://laravel-projet-gestion-comptes.onrender.com/api/v1",
 *     description="Serveur de production"
 * )
 */
class CompteController extends Controller
{
    use ApiResponse;

    protected $compteService;

    public function __construct(CompteService $compteService)
    {
        $this->compteService = $compteService;
    }

    /**
     * Récupère les détails d'un compte spécifique
     * 
     * @OA\Get(
     *     path="/comptes/{numero}",
     *     tags={"Comptes"},
     *     summary="Récupère les détails d'un compte spécifique",
     *     @OA\Parameter(
     *         name="numero",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Succès"),
     *     @OA\Response(response="404", description="Compte non trouvé")
     * )
     */
    public function show($numero)
    {
        \Log::info("Requête show pour compte: {$numero}");
        try {
            $compte = $this->compteService->findByNumero($numero);
            \Log::info("Compte trouvé et retourné: {$numero}");
            return $this->success(new CompteResource($compte));
        } catch (\Exception $e) {
            \Log::error("Erreur dans show compte {$numero}: " . $e->getMessage());
            return $this->error($e->getMessage(), 404);
        }
    }

    /**
     * Lister tous les comptes
     * 
     * @OA\Get(
     *     path="/comptes",
     *     summary="Liste tous les comptes avec pagination et filtres",
     *     tags={"Comptes"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche par titulaire ou numéro de compte",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filtre par type de compte",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         description="Filtre par statut de compte",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Direction du tri (asc/desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Champ pour le tri",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "solde", "titulaire", "type", "statut", "numero_compte"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre d'éléments par page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des comptes récupérée avec succès"
     *     )
     * )
     */
    public function index(Request $request)
    {
        \Log::info("Requête index comptes", ['params' => $request->query()]);
        try {
            $result = $this->compteService->listerComptes($request->query());
            \Log::info("Liste comptes récupérée avec succès", ['count' => count($result['data'])]);

            return $this->success([
                'data' => CompteResource::collection($result['data']),
                'pagination' => $result['pagination'],
                'links' => $result['links'],
            ]);
        } catch (\Exception $e) {
            \Log::error("Erreur dans index comptes: " . $e->getMessage());
            return $this->error('Erreur lors de la récupération des comptes', 500);
        }
    }
}