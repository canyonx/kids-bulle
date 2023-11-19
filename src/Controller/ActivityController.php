<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ChildRepository;
use App\Form\ActivityAddChildrenType;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/activity")
 */
class ActivityController extends AbstractController
{
    /**
     * @Route("/{id}", name="app_activity_show", methods={"GET", "POST"})
     */
    public function show(
        Activity $activity,
        Request $request,
        ActivityRepository $activityRepository
    ): Response {
        // Form to add child in activity
        $form = $this->createForm(ActivityAddChildrenType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $childs = $form->get('addChildrens')->getData();
            // Add childrens to activity
            foreach ($childs as $child) {
                $activity->addChildren($child);
            }
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activity/show.html.twig', [
            'activity' => $activity,
            'form' => $form
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_activity_edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        Activity $activity,
        ActivityRepository $activityRepository
    ): Response {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/remove/{child}", name="app_activity_remove_child", methods={"GET"})
     */
    public function removeChild(
        Activity $activity,
        Child $child,
        ActivityRepository $activityRepository
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $activity->removeChildren($child);
        $activityRepository->add($activity, true);

        return $this->redirectToRoute('app_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
    }
}
