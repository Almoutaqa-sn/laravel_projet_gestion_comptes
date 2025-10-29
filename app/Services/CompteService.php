<?php

namespace App\Services;

use App\Models\Compte;

class CompteService
{
    /**
     * Trouve un compte par son numéro
     *
     * @param string $numero
     * @return Compte
     * @throws CompteNotFoundException
     */
    public function findByNumero($numero)
    {
        \Log::info("Recherche du compte numéro: {$numero}");
        try {
            $compte = Compte::where('numero_compte', $numero)->first();

            if (!$compte) {
                \Log::warning("Compte non trouvé: {$numero}");
                throw new \App\Exceptions\CompteNotFoundException("Le compte avec le numéro {$numero} n'a pas été trouvé.");
            }

            \Log::info("Compte trouvé: {$numero}");
            return $compte;
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la recherche du compte {$numero}: " . $e->getMessage());
            throw $e;
        }
    }
    public function listerComptes(array $params)
    {
        \Log::info('Params reçus dans listerComptes:', $params);
        $query = Compte::query();

        // Filtre par défaut : exclure les comptes BLOQUE et FERME
        $query->whereNotIn('statut', ['BLOQUE', 'FERME']);

        // Filtre par type
        if (!empty($params['type'])) {
            \Log::info('Application du filtre type:', ['type' => $params['type']]);
            $query->where('type', $params['type']);
        }

        // Filtre par statut (si spécifié explicitement)
        if (!empty($params['statut'])) {
            \Log::info('Application du filtre statut:', ['statut' => $params['statut']]);
            $query->where('statut', $params['statut']);
        }

        // Recherche par titulaire ou numéro
        if (!empty($params['search'])) {
            \Log::info('Application du filtre search:', ['search' => $params['search']]);
            $query->where(function ($q) use ($params) {
                $q->where('titulaire', 'ilike', '%' . $params['search'] . '%')
                  ->orWhere('numero_compte', 'ilike', '%' . $params['search'] . '%');
            });
        }

        // Tri
        $allowedSortFields = ['created_at', 'solde', 'titulaire', 'type', 'statut', 'numero_compte'];
        $sortField = $params['sort'] ?? 'created_at';
        $sort = in_array($sortField, $allowedSortFields) ? $sortField : 'created_at';
        $orderField = $params['order'] ?? 'desc';
        $order = in_array(strtolower($orderField), ['asc', 'desc']) ? strtolower($orderField) : 'desc';
        $query->orderBy($sort, $order);

        // Pagination
        $limit = min($params['limit'] ?? 10, 100);
        $page = $params['page'] ?? 1;

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        // Format des liens pour l’API
        $links = [
            'self' => $paginator->url($paginator->currentPage()),
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage()),
            'next' => $paginator->nextPageUrl(),
            'previous' => $paginator->previousPageUrl(),
        ];

        return [
            'data' => $paginator->items(),
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'totalPages' => $paginator->lastPage(),
                'totalItems' => $paginator->total(),
                'itemsPerPage' => $paginator->perPage(),
                'hasNext' => $paginator->hasMorePages(),
                'hasPrevious' => $paginator->currentPage() > 1,
            ],
            'links' => $links,
        ];
    }

    public function recupererCompteParNumero(string $numero)
    {
        \Log::info("Récupération du compte par numéro: {$numero}");
        try {
            $compte = Compte::numero($numero)->first();

            if (!$compte) {
                \Log::warning("Compte non trouvé via scope: {$numero}");
                throw new \App\Exceptions\CompteNotFoundException("Compte non trouvé");
            }

            return $compte;
        } catch (\Exception $e) {
            \Log::error("Erreur récupération compte {$numero}: " . $e->getMessage());
            throw $e;
        }
    }
}
