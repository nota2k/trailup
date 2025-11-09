<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/geo')]
class GeoApiController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    #[Route('/search', name: 'api_geo_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        
        if (strlen($query) < 2) {
            return new JsonResponse([]);
        }

        try {
            // Utilisation de l'API Géo de l'État français
            $response = $this->httpClient->request('GET', 'https://geo.api.gouv.fr/communes', [
                'query' => [
                    'nom' => $query,
                    'limit' => 10,
                    'fields' => 'nom,code,codesPostaux,codeDepartement,codeRegion'
                ]
            ]);

            $communes = $response->toArray();
            
            // Formater les résultats pour TomSelect
            $results = [];
            foreach ($communes as $commune) {
                $nom = $commune['nom'];
                $codePostal = isset($commune['codesPostaux'][0]) ? $commune['codesPostaux'][0] : '';
                
                // Si plusieurs codes postaux, créer une entrée pour chacun
                if (isset($commune['codesPostaux']) && count($commune['codesPostaux']) > 1) {
                    foreach ($commune['codesPostaux'] as $cp) {
                        $results[] = [
                            'value' => $nom . ' (' . $cp . ')',
                            'text' => $nom . ' (' . $cp . ')',
                            'codePostal' => $cp,
                            'ville' => $nom,
                            'code' => $commune['code']
                        ];
                    }
                } else {
                    $results[] = [
                        'value' => $nom . ($codePostal ? ' (' . $codePostal . ')' : ''),
                        'text' => $nom . ($codePostal ? ' (' . $codePostal . ')' : ''),
                        'codePostal' => $codePostal,
                        'ville' => $nom,
                        'code' => $commune['code']
                    ];
                }
            }

            return new JsonResponse($results);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la recherche'], 500);
        }
    }

    #[Route('/search-by-postal', name: 'api_geo_search_by_postal', methods: ['GET'])]
    public function searchByPostal(Request $request): JsonResponse
    {
        $codePostal = $request->query->get('cp', '');
        
        if (strlen($codePostal) < 2) {
            return new JsonResponse([]);
        }

        try {
            // Recherche par code postal
            $response = $this->httpClient->request('GET', 'https://geo.api.gouv.fr/communes', [
                'query' => [
                    'codePostal' => $codePostal,
                    'limit' => 20,
                    'fields' => 'nom,code,codesPostaux,codeDepartement,codeRegion'
                ]
            ]);

            $communes = $response->toArray();
            
            // Formater les résultats pour TomSelect
            $results = [];
            foreach ($communes as $commune) {
                $nom = $commune['nom'];
                $cp = isset($commune['codesPostaux'][0]) ? $commune['codesPostaux'][0] : $codePostal;
                
                $results[] = [
                    'value' => $nom . ' (' . $cp . ')',
                    'text' => $nom . ' (' . $cp . ')',
                    'codePostal' => $cp,
                    'ville' => $nom,
                    'code' => $commune['code']
                ];
            }

            return new JsonResponse($results);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la recherche'], 500);
        }
    }

    #[Route('/geocode', name: 'api_geo_geocode', methods: ['GET'])]
    public function geocode(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $codePostal = $request->query->get('cp', '');
        
        if (empty($query)) {
            return new JsonResponse(['error' => 'Ville requise'], 400);
        }

        try {
            // Utiliser Nominatim (OpenStreetMap) pour le géocodage
            $searchQuery = $query;
            if ($codePostal) {
                $searchQuery .= ', ' . $codePostal;
            }
            $searchQuery .= ', France';
            
            $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $searchQuery,
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1
                ],
                'headers' => [
                    'User-Agent' => 'TrailUp/1.0'
                ]
            ]);

            $results = $response->toArray();
            
            if (empty($results)) {
                return new JsonResponse(['error' => 'Aucun résultat trouvé'], 404);
            }
            
            $result = $results[0];
            
            return new JsonResponse([
                'lat' => (float) $result['lat'],
                'lon' => (float) $result['lon'],
                'display_name' => $result['display_name']
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors du géocodage: ' . $e->getMessage()], 500);
        }
    }
}

