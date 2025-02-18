<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CovoiturageRepository;
use Symfony\Component\HttpFoundation\Response;

class SearchCovoiturageController extends AbstractController
{
    #[Route('/search/show', name: 'app_search_show')]
public function show(): Response
{
  return $this->render('search_covoiturage/search/show.html.twig', [
            'controller_name' => 'SearchCovoiturageController',
        ]); 
}


#[Route('/search/covoiturage', name: 'app_search_covoiturage', methods: ['GET'])]
public function index(): Response
{
    return $this->render('search_covoiturage/index.html.twig', [
        'controller_name' => 'SearchCovoiturageController',
    ]);
}



    #[Route('/api/search', name: 'api_search_covoiturage', methods: ['GET'])]
    public function search(Request $request, CovoiturageRepository $covoiturageRepository): JsonResponse
{
    // Récupération des paramètres
    $pointDepart = $request->query->get('pointDepart');
    $pointArrivee = $request->query->get('pointArrivee');
    $dateDepart = $request->query->get('dateDepart');
    $heureDepart = $request->query->get('heureDepart');

    // Validation des paramètres obligatoires
    if (!$pointDepart || !$pointArrivee || !$dateDepart) {
        return $this->json([
            'error' => 'Les champs "pointDepart", "pointArrivee" et "dateDepart" sont obligatoires.'
        ], 400);
    }

    // Conversion de la date et de l’heure
    try {
        $dateDepartObj = new \DateTime($dateDepart);
        $heureDepartObj = $heureDepart ? \DateTime::createFromFormat('H:i', $heureDepart) : null;

        if ($heureDepart && !$heureDepartObj) {
            return $this->json([
                'error' => 'Heure invalide.'
            ], 400);
        }
    } catch (\Exception $e) {
        return $this->json([
            'error' => 'Date ou heure invalide : ' . $e->getMessage()
        ], 400);
    }

    // Recherche des trajets exacts via le repository
    try {
        $exactMatches = $covoiturageRepository->findTrajets(
            $pointDepart,
            $pointArrivee,
            $dateDepartObj,
            $heureDepartObj
        );
    } catch (\Exception $e) {
        return $this->json([
            'error' => 'Erreur lors de la recherche dans la base de données : ' . $e->getMessage()
        ], 500);
    }

    // Si aucun trajet exact trouvé, chercher les suggestions
    if (empty($exactMatches)) {
        try {
            $suggestedMatches = $covoiturageRepository->findTrajets(
                $pointDepart,
                $pointArrivee,
                $dateDepartObj,
                null // Recherche sans l'heure
            );
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur lors de la recherche de suggestions : ' . $e->getMessage()
            ], 500);
        }

        if (empty($suggestedMatches)) {
            return $this->json([
                'message' => 'Aucun trajet trouvé pour les critères spécifiés.'
            ], 404);
        }

        // Transformation des suggestions en données sérialisables
        $suggestedData = array_map([$this, 'mapTrajet'], $suggestedMatches);

        return $this->json([
            'exactMatches' => [],
            'suggestedMatches' => $suggestedData
        ], 200);
    }

    // Transformation des résultats exacts en données sérialisables
    $exactData = array_map([$this, 'mapTrajet'], $exactMatches);

    return $this->json([
        'exactMatches' => $exactData,
        'suggestedMatches' => [] // Pas de suggestions si des résultats exacts existent
    ], 200);
}

// Méthode pour mapper un trajet en tableau sérialisable
private function mapTrajet($trajet): array
{
    try {
        return [
            'id' => $trajet['id'],
            'pointDepart' => $trajet['pointDepart'],
            'pointArrivee' => $trajet['pointArrivee'],
            'dateDepart' => isset($trajet['dateDepart']) ? (new \DateTime($trajet['dateDepart']))->format('Y-m-d') : null,
            'dateArrivee' => isset($trajet['dateArrivee']) ? (new \DateTime($trajet['dateArrivee']))->format('Y-m-d') : null,
            'heureDepart' => isset($trajet['heureDepart']) ? (new \DateTime($trajet['heureDepart']))->format('H:i') : null,
            'heureArrivee' => isset($trajet['heureArrivee']) ? (new \DateTime($trajet['heureArrivee']))->format('H:i') : null,
            'nbPlace' => $trajet['nbPlace'] ?? 0, // Valeur par défaut si manquante
            'prix' => $trajet['prix'] ?? 0.0,    // Valeur par défaut si manquante
        ];
    } catch (\Exception $e) {
        return [
            'error' => 'Erreur lors du mappage du trajet : ' . $e->getMessage()
        ];
    }
}
}