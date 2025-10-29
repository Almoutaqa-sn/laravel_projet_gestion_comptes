<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CompteService;
use App\Http\Resources\CompteResource;
use App\Http\Requests\StoreCompteRequest;
use App\Traits\ApiResponse;
use App\Models\Compte;
use App\Models\Client;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    /**
     * Créer un nouveau compte
     *
     * @OA\Post(
     *     path="/comptes",
     *     tags={"Comptes"},
     *     summary="Créer un nouveau compte bancaire",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type", "soldeInitial", "devise", "client"},
     *             @OA\Property(property="type", type="string", enum={"CHEQUE", "EPARGNE"}, example="cheque"),
     *             @OA\Property(property="soldeInitial", type="number", minimum=10000, example=500000),
     *             @OA\Property(property="devise", type="string", enum={"FCFA", "XOF", "USD", "EUR"}, example="FCFA"),
     *             @OA\Property(property="client", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", nullable=true, example=null),
     *                 @OA\Property(property="titulaire", type="string", example="Hawa BB Wane"),
     *                 @OA\Property(property="nci", type="string", example="1234567890123", nullable=true),
     *                 @OA\Property(property="email", type="string", format="email", example="cheikh.sy@example.com"),
     *                 @OA\Property(property="telephone", type="string", example="+221771234567"),
     *                 @OA\Property(property="adresse", type="string", example="Dakar, Sénégal")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compte créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Compte créé avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="660f9511-f30c-52e5-b827-557766551111"),
     *                 @OA\Property(property="numeroCompte", type="string", example="C00123460"),
     *                 @OA\Property(property="titulaire", type="string", example="Cheikh Sy"),
     *                 @OA\Property(property="type", type="string", example="cheque"),
     *                 @OA\Property(property="solde", type="number", example=500000),
     *                 @OA\Property(property="devise", type="string", example="FCFA"),
     *                 @OA\Property(property="dateCreation", type="string", format="date-time", example="2025-10-19T10:30:00Z"),
     *                 @OA\Property(property="statut", type="string", example="actif"),
     *                 @OA\Property(property="metadata", type="object",
     *                     @OA\Property(property="derniereModification", type="string", format="date-time", example="2025-10-19T10:30:00Z"),
     *                     @OA\Property(property="version", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="code", type="string", example="VALIDATION_ERROR"),
     *                 @OA\Property(property="message", type="string", example="Les données fournies sont invalides"),
     *                 @OA\Property(property="details", type="object",
     *                     @OA\Property(property="client.telephone", type="array", @OA\Items(type="string", example="Le numéro de téléphone doit être un numéro sénégalais valide"))
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreCompteRequest $request)
    {
        \Log::info("Création d'un nouveau compte", ['data' => $request->all()]);

        try {
            // Récupérer ou créer le client
            $clientData = $request->input('client');
            $client = null;

            if (!empty($clientData['id'])) {
                // Client existant
                $client = Client::find($clientData['id']);
                if (!$client) {
                    return $this->error('Client non trouvé', 404);
                }
            } else {
                // Créer un nouveau client
                $password = Str::random(12); // Générer un mot de passe temporaire
                $code = strtoupper(Str::random(6)); // Générer un code de vérification

                $client = Client::create([
                    'nom' => explode(' ', $clientData['titulaire'])[0] ?? $clientData['titulaire'],
                    'prenom' => explode(' ', $clientData['titulaire'])[1] ?? '',
                    'email' => $clientData['email'],
                    'telephone' => $clientData['telephone'],
                    'adresse' => $clientData['adresse'],
                ]);

                // Stocker temporairement le mot de passe et le code (dans un vrai projet, utiliser une table séparée)
                $client->temporary_password = $password;
                $client->verification_code = $code;
                $client->save();

                \Log::info("Nouveau client créé", ['client_id' => $client->id, 'email' => $client->email]);
            }

            // Récupérer un admin au hasard pour l'association
            $admin = Admin::inRandomOrder()->first();
            if (!$admin) {
                return $this->error('Aucun administrateur disponible', 500);
            }

            // Créer le compte
            $compte = Compte::create([
                'titulaire' => $clientData['titulaire'],
                'type' => $request->input('type'),
                'solde' => $request->input('soldeInitial'), // Le solde sera calculé dynamiquement
                'devise' => $request->input('devise'),
                'statut' => 'ACTIF',
                'client_id' => $client->id,
                'admin_id' => $admin->id,
            ]);

            // Créer une transaction de dépôt initial
            $compte->transactions()->create([
                'type' => 'DEPOT',
                'montant' => $request->input('soldeInitial'),
                'devise' => $request->input('devise'),
                'description' => 'Dépôt initial lors de la création du compte',
                'statut' => 'VALIDEE',
                'admin_id' => $admin->id,
            ]);

            \Log::info("Compte créé avec succès", [
                'compte_id' => $compte->id,
                'numero_compte' => $compte->numero_compte,
                'client_id' => $client->id
            ]);

            // Déclencher l'événement de création du compte
            \App\Events\CompteCreated::dispatch($compte, $client);

            return $this->success([
                'id' => $compte->id,
                'numeroCompte' => $compte->numero_compte,
                'titulaire' => $compte->titulaire,
                'type' => strtolower($compte->type),
                'solde' => $compte->solde,
                'devise' => $compte->devise,
                'dateCreation' => $compte->created_at->toISOString(),
                'statut' => strtolower($compte->statut),
                'metadata' => [
                    'derniereModification' => $compte->updated_at->toISOString(),
                    'version' => $compte->version,
                ]
            ], 'Compte créé avec succès', 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning("Erreur de validation lors de la création du compte", [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Les données fournies sont invalides',
                    'details' => $e->errors()
                ]
            ], 400);
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la création du compte: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error('Erreur interne du serveur', 500);
        }
    }
}