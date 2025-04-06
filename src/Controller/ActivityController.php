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

#[Route(path: '/activity')]
class ActivityController extends AbstractController
{
    #[Route(path: '/{id}', name: 'app_activity_show', methods: ['GET', 'POST'])]
    public function show(
        Activity $activity,
        ChildRepository $childRepository,
    ): Response {
        /** @var User */
        $user = $this->getUser();

        // add user childs to an activity from the view
        // $enrolledChildren = $activity->getChildrens()->toArray();
        $enrolledChildren = $childRepository->findByActivityOrderedByUserAndBirth($activity, $user);
        $userChildren = $user->getChilds()->toArray();

        // $enrolledChildrenIds = array_map(fn($child) => $child->getId(), $enrolledChildren);
        $notEnrolledChildren = array_filter($userChildren, fn($child) => !in_array($child, $enrolledChildren));

        return $this->render('activity/show.html.twig', [
            'activity' => $activity,
            'activityChildren' => $enrolledChildren,
            'notEnrolledChildren' => $notEnrolledChildren
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

        return $this->redirectToRoute('app_activity_show', [
            'id' => $activity->getId()
        ], Response::HTTP_SEE_OTHER);
    }

    /**
     * User can add his own child to an activity
     */
    #[Route(path: '/{id}/add/{child}', name: 'app_activity_add_child', methods: ['GET'])]
    public function addChild(
        Activity $activity,
        Child $child,
        ActivityRepository $activityRepository
    ): Response {

        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $activity->addChildren($child);
        $activityRepository->add($activity, true);

        return $this->redirectToRoute('app_activity_show', [
            'id' => $activity->getId()
        ], Response::HTTP_SEE_OTHER);
    }
}
