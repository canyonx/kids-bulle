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

        dump($request->headers->get('referer'));

        // return $this->redirect($request->headers->get('referer'), Response::HTTP_SEE_OTHER);
        // Récupérer l'URL de la page précédente (referer)
        $referer = $request->headers->get('referer');
        // Si le referer contient un paramètre 'date', on l'utilise
        if ($referer && preg_match('/\?date=([0-9\-]+)/', $referer, $matches)) {
            // Si un paramètre de date est trouvé dans le referer, on le conserve dans la redirection
            $date = $matches[1];
        }

        // Rediriger en fonction du referer (avec ou sans date)
        if ($date) {
            // Si la date est présente, rediriger vers l'URL de l'activité avec la date
            return $this->redirectToRoute('app_activity_show', [
                'id' => $activity->getId(),
                'date' => $date,
            ], Response::HTTP_SEE_OTHER);
        } else {
            // Sinon, rediriger vers la page d'activité sans paramètre de date
            return $this->redirectToRoute('app_activity_show', [
                'id' => $activity->getId(),
            ], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route(path: '/{id}', name: 'app_activity_show', methods: ['GET', 'POST'])]
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

    // /**
    //  * User can remove is own child from an activity
    //  */
    // #[Route(path: '/{id}/remove/{child}', name: 'app_activity_remove_child', methods: ['GET'])]
    // public function removeChild(
    //     Activity $activity,
    //     Child $child,
    //     ActivityRepository $activityRepository
    // ): Response {

    //     $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

    //     $activity->removeChildren($child);
    //     $activityRepository->add($activity, true);

    //     return $this->redirectToRoute('app_activity_show', [
    //         'id' => $activity->getId()
    //     ], Response::HTTP_SEE_OTHER);
    // }

    // /**
    //  * User can add his own child to an activity
    //  */
    // #[Route(path: '/{id}/add/{child}', name: 'app_activity_add_child', methods: ['GET'])]
    // public function addChild(
    //     Activity $activity,
    //     Child $child,
    //     ActivityRepository $activityRepository
    // ): Response {

    //     $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

    //     $activity->addChildren($child);
    //     $activityRepository->add($activity, true);

    //     return $this->redirectToRoute('app_activity_show', [
    //         'id' => $activity->getId()
    //     ], Response::HTTP_SEE_OTHER);
    // }

}
