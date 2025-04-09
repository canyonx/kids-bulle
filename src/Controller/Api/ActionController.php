<?php

namespace App\Controller\Api;

use App\Repository\ActivityRepository;
use App\Repository\ChildRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ActivityActionController extends AbstractController
{
    #[Route('/api/activity/action', name: 'app_api_activity_action', methods: ['POST'])]
    public function activityAction(
        Request $request,
        ActivityRepository $activityRepository,
        ChildRepository $childRepository
    ): JsonResponse {
        // Récupération des données de la requête
        $childId = $request->request->get('child');
        $activityId = $request->request->get('activity');
        $action = $request->request->get('action');

        // Validation des données
        if (!$childId || !$activityId || !in_array($action, ['add', 'remove'])) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid data provided',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Récupération des entités
        $activity = $activityRepository->find($activityId);
        $child = $childRepository->find($childId);

        if (!$activity || !$child) {
            return $this->json([
                'success' => false,
                'message' => 'Activity or Child not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // Vérification des permissions
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        // Gestion de l'action
        if ($action === 'remove') {
            $activity->removeChildren($child);
        } elseif ($action === 'add') {
            $activity->addChildren($child);
        }

        // Persistance des modifications
        $activityRepository->add($activity, true);

        // Réponse JSON
        return $this->json([
            'success' => true,
            'message' => sprintf('Child %s successfully %sed from activity %s', $child->getId(), $action, $activity->getId()),
        ]);
    }
}
