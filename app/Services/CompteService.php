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
        $compte = Compte::where('numero_compte', $numero)->first();
        
        if (!$compte) {
            throw new \App\Exceptions\CompteNotFoundException("Le compte avec le numéro {$numero} n'a pas été trouvé.");
        }
        
        return $compte;
    }
    public function listerComptes(array $params)
    {
        \Log::info('Params reçus dans listerComptes:', $params);
        $query = Compte::query();

        // Filtre par type
        if (!empty($params['type'])) {
            \Log::info('Application du filtre type:', ['type' => $params['type']]);
            $query->where('type', $params['type']);
        }

        // Filtre par statut
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
        $compte = Compte::numero($numero)->first();

        if (!$compte) {
            throw new CompteNotFoundException();
        }

        return $compte;
    }
}
