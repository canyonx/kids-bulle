<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Child;
use App\Entity\Activity;
use App\Repository\ChildRepository;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Json;

#[Route(path: '/activity')]
class ActivityController extends AbstractController
{
    /**
     * User can add/remove is own child from an activity
     */
    #[Route(path: '/action', name: 'app_activity_action', methods: ['POST'])]
    public function action(
        Request $request,
        ActivityRepository $activityRepository,
        ChildRepository $childRepository
    ): Response {
        // data from request
        $childId = $request->request->get('child');
        $activityId = $request->request->get('activity');
        $action = $request->request->get('action');
        $date = $request->request->get('date');

        // dump($request->request->all());
        // objects from database
        $activity = $activityRepository->find($activityId);
        $child = $childRepository->find($childId);

        if (
            !$activity ||
            !$child ||
            (!in_array($action, ['add', 'remove']))
        ) {
            throw $this->createNotFoundException('Action, Activity or Child not found');
        }

        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        if ($action === 'remove') {
            $activity->removeChildren($child);
        } elseif ($action === 'add') {
            $activity->addChildren($child);
        }

        $activityRepository->add($activity, true);

        return $this->redirect($request->headers->get('referer'), Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}', name: 'app_activity_show', methods: ['GET'])]
    public function show(
        Activity $activity,
        ChildRepository $childRepository,
    ): Response {
        /** @var User */
        $user = $this->getUser();

        // add user childs to an activity from the view
        $enrolledChildren = $childRepository->findByActivityOrderedByUserAndBirth($activity, $user);
        $userChildren = $user->getChilds()->toArray();
        // filter user children not enrolled in the activity
        $notEnrolledChildren = array_filter($userChildren, fn($child) => !in_array($child, $enrolledChildren));

        return $this->render('activity/show.html.twig', [
            'activity' => $activity,
            'enrolledChildren' => $enrolledChildren,
            'notEnrolledChildren' => $notEnrolledChildren
        ]);
    }
}
