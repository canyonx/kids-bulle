<?php

namespace App\Controller\Admin;

use App\Entity\Child;
use App\Entity\Activity;
use App\Form\ActivityType;
use App\Service\PlanningService;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/activity")
 */
class ActivityController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_activity_index", methods={"GET"})
     */
    public function index(ActivityRepository $activityRepository, PlanningService $planningService): Response
    {
        $activities = $activityRepository->findByDateBetween();

        $planning = $planningService->getPlanning($activities);

        return $this->render('admin/activity/index.html.twig', [
            'planning' => $planning
        ]);
    }

    /**
     * @Route("/new", name="app_admin_activity_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ActivityRepository $activityRepository): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Add childs of a category to a new activity
            $childs = $activity->getCategory()->getChilds();
            foreach ($childs as $child) {
                $activity->addChildren($child);
            }
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_admin_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_activity_show", methods={"GET"})
     */
    public function show(Activity $activity): Response
    {
        return $this->render('admin/activity/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admin_activity_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Activity $activity, ActivityRepository $activityRepository): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_admin_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_activity_delete", methods={"POST"})
     */
    public function delete(Request $request, Activity $activity, ActivityRepository $activityRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $activity->getId(), $request->request->get('_token'))) {
            $activityRepository->remove($activity, true);
        }

        return $this->redirectToRoute('app_admin_activity_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/remove/{child}", name="app_admin_activity_remove_child", methods={"GET"})
     */
    public function removeChild(
        Activity $activity,
        Child $child,
        ActivityRepository $activityRepository
    ): Response {

        $activity->removeChildren($child);
        $activityRepository->add($activity, true);

        return $this->redirectToRoute('app_admin_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
    }
}
