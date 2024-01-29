<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/activity')]
class ActivityController extends AbstractController
{
    #[Route(path: '/{id}', name: 'app_activity_show', methods: ['GET', 'POST'])]
    public function show(
        Activity $activity
    ): Response {
        return $this->render('activity/show.html.twig', [
            'activity' => $activity
        ]);
    }

    /**
     * User can remove is own child from an activity
     */
    #[Route(path: '/{id}/remove/{child}', name: 'app_activity_remove_child', methods: ['GET'])]
    public function removeChild(
        Activity $activity,
        Child $child,
        ActivityRepository $activityRepository
    ): Response {

        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $activity->removeChildren($child);
        $activityRepository->add($activity, true);

        return $this->redirectToRoute('app_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
    }
}
