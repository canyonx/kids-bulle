<?php

namespace App\Controller\Admin;

use App\Entity\Child;
use App\Entity\Activity;
use App\Form\ActivityType;
use App\Service\PlanningService;
use App\Form\AddChildToActivityType;
use App\Form\MoveChildsToActivityType;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin/activity')]
class ActivityController extends AbstractController
{
    #[Route(path: '/', name: 'app_admin_activity_index', methods: ['GET'])]
    public function index(Request $request, ActivityRepository $activityRepository, PlanningService $planningService): Response
    {
        if ($request->get('date')) {
            $dateStart = new \DateTimeImmutable($request->get('date'));
        } else {
            $dateStart = new \DateTimeImmutable('today', new \DateTimeZone("Europe/Paris"));
        }

        $activities = $activityRepository->findByDateBetween($dateStart);

        $planning = $planningService->getPlanning($activities, $dateStart);
        return $this->render('admin/activity/index.html.twig', [
            'planning' => $planning
        ]);
    }

    #[Route(path: '/new', name: 'app_admin_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ActivityRepository $activityRepository): Response
    {
        $activity = new Activity();
        // Dat par defaut demain 9h
        $datetime = new \DateTime('tomorrow');
        $datetime = $datetime->format('Y-m-d 9:00');
        $activity->setDateAt(new \DateTimeImmutable($datetime), new \DateTimeZone("Europe/Paris"));

        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupére le teacher de la category
            $activity->setTeacher($activity->getCategory()->getTeacher());
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_admin_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route(path: '/{id}', name: 'app_admin_activity_show', methods: ['GET', 'POST'])]
    public function show(Activity $activity, Request $request, ActivityRepository $activityRepository): Response
    {
        $form = $this->createForm(AddChildToActivityType::class, null, ['activity' => $activity]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form["childrens"]->getData() as $child) {
                $activity->addChildren($child);
            }
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_admin_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/show.html.twig', [
            'activity' => $activity,
            'form' => $form
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'app_admin_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activity $activity, ActivityRepository $activityRepository): Response
    {
        $form = $this->createForm(ActivityType::class, $activity, ['edit' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_admin_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'app_admin_activity_delete', methods: ['POST'])]
    public function delete(Request $request, Activity $activity, ActivityRepository $activityRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $activity->getId(), $request->request->get('_token'))) {
            $activityRepository->remove($activity, true);
        }

        return $this->redirectToRoute('app_admin_activity_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/remove/{child}', name: 'app_admin_activity_remove_child', methods: ['GET'])]
    public function removeChild(
        Activity $activity,
        Child $child,
        ActivityRepository $activityRepository
    ): Response {

        $activity->removeChildren($child);
        $activityRepository->add($activity, true);

        return $this->redirectToRoute('app_admin_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/move-childs', name: 'app_admin_activity_move_child', methods: ['GET', 'POST'])]
    public function moveChilds(
        Activity $activity,
        Request $request,
        ActivityRepository $activityRepository
    ): Response {

        $form = $this->createForm(MoveChildsToActivityType::class, null, [
            'activity' => $activity
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $childs = $activity->getChildrens();

            $toActivity = $form['activity']->getData();
            foreach ($childs as $child) {
                $toActivity->addChildren($child);
                $activityRepository->add($toActivity, true);
            }

            $activity->getChildrens()->clear();
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_admin_activity_show', ['id' => $toActivity->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/move_childs.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }
}
