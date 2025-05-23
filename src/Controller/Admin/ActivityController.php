<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Activity;
use App\Form\ActivityType;
use App\Service\PlanningService;
use App\Service\StartDateService;
use App\Form\SelectChildsFormType;
use App\Repository\ChildRepository;
use App\Form\AddChildToActivityType;
use App\Form\MoveChildsToActivityType;
use App\Repository\ActivityRepository;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin/activity')]
class ActivityController extends AbstractController
{
    /**
     * Admin Activity List starting from today
     *
     * @param Request $request
     * @param ActivityRepository $activityRepository
     * @param PlanningService $planningService
     * @param StartDateService $startDateService
     * @return Response
     */
    #[Route(path: '/', name: 'app_admin_activity_index', methods: ['GET'])]
    public function index(): Response
    {
        throw new \Exception('This page is deprecated, please use the new one : app_planning');
    }

    /**
     * Admin, Activity List for a specific user
     *
     * @param User $user
     * @param Request $request
     * @param ActivityRepository $activityRepository
     * @param PlanningService $planningService
     * @param StartDateService $startDateService
     * @return Response
     */
    #[Route(path: '/user/{id}', name: 'app_admin_activity_user', methods: ['GET'])]
    public function user(
        User $user,
        Request $request,
        ActivityRepository $activityRepository,
        PlanningService $planningService,
        StartDateService $startDateService
    ): Response {
        $dateStart = $startDateService->getStartDate($request->get('date'));

        $activities = $activityRepository->findByUser($user, $dateStart);

        $planning = $planningService->getPlanning($activities, $dateStart);
        return $this->render('admin/user/activities.html.twig', [
            'planning' => $planning,
            'user' => $user,
            'childs' => $user->getChilds()
        ]);
    }

    /**
     * Trip Create
     */
    #[Route(path: '/new', name: 'app_admin_activity_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ActivityRepository $activityRepository,
        ConfigService $configService,
    ): Response {
        // Check config allowTeacherCreation
        if (
            $configService->get('allowTeacherCreation') === 'false'
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour effectuer cette action.');
            return $this->redirectToRoute('app_planning', [], Response::HTTP_SEE_OTHER);
        }

        $activity = new Activity();
        // Default Date tomorrow 9h
        $datetime = new \DateTime('tomorrow');
        $datetime = $datetime->format('Y-m-d 9:00');
        $activity->setDateAt(new \DateTimeImmutable($datetime), new \DateTimeZone("Europe/Paris"));

        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get category teacher
            $activity->setTeacher($activity->getCategory()->getTeacher());
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_planning', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    /**
     * Trip Details
     */
    #[Route(path: '/{id}', name: 'app_admin_activity_show', methods: ['GET', 'POST'])]
    public function show(
        Activity $activity,
        Request $request,
        ActivityRepository $activityRepository
    ): Response {
        // Form to move or remove childs from activity
        $selectChildsForm = $this->createForm(SelectChildsFormType::class, null, ['activity' => $activity]);
        $selectChildsForm->handleRequest($request);
        if ($selectChildsForm->isSubmitted() && $selectChildsForm->isValid()) {

            // If REMOVE
            if ($selectChildsForm->get('remove')->isClicked() && count($selectChildsForm->getData()->getChildrens()) > 0) {
                foreach ($selectChildsForm->getData()->getChildrens() as $c) {
                    $activity->removeChildren($c);
                }
                $activityRepository->add($activity, true);
            }

            // If MOVE
            if ($selectChildsForm->get('move')->isClicked() && count($selectChildsForm->getData()->getChildrens()) > 0) {
                $childs = [];
                foreach ($selectChildsForm->getData()->getChildrens() as $c) {
                    $childs[] = $c->getId();
                }
                // $childrens = serialize($childrens->toArray());
                return $this->redirectToRoute('app_admin_activity_move_childs', [
                    'id' => $activity->getId(),
                    'childrens' => $childs
                ], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_admin_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
        }

        // Admin form to add childs to activity
        $form = $this->createForm(AddChildToActivityType::class, null, ['activity' => $activity]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData()->getChildrens() as $child) {
                $activity->addChildren($child);
                $activityRepository->add($activity, true);
            }

            return $this->redirectToRoute('app_admin_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/show.html.twig', [
            'activity' => $activity,
            'form' => $form,
            'selectChildsForm' => $selectChildsForm
        ]);
    }

    /**
     * Move selected childs to another activity the same day
     * Remove selected childs from activity
     */
    #[Route(path: '/{id}/move', name: 'app_admin_activity_move_childs', methods: ['GET', 'POST'])]
    public function move(
        Activity $activity,
        Request $request,
        ChildRepository $childRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Array of childs id
        $childrens = $request->get('childrens');

        if (empty($childrens)) {
            $this->addFlash('error', 'Aucun enfant sélectionné.');
            return $this->redirectToRoute('app_admin_activity_show', ['id' => $activity->getId()]);
        }

        $form = $this->createForm(MoveChildsToActivityType::class, null, ['activity' => $activity]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Activity */
            $toActivity = $form['activity']->getData();
            foreach ($childrens as $child) {
                $child = $childRepository->find($child);
                // Add child to new activity
                $toActivity->addChildren($child);
                // $activityRepository->add($toActivity, true);
                $entityManager->persist($toActivity);
                // Remove child from activity
                $activity->removeChildren($child);
                // $activityRepository->add($activity, true);
                $entityManager->persist($activity);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Les enfants ont été déplacés avec succès.');

            return $this->redirectToRoute('app_admin_activity_show', ['id' => $activity->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/move_childs.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    /**
     * Trip Edit
     */
    #[Route(path: '/{id}/edit', name: 'app_admin_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activity $activity, ActivityRepository $activityRepository): Response
    {
        $form = $this->createForm(ActivityType::class, $activity, ['edit' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $activityRepository->add($activity, true);

            return $this->redirectToRoute('app_planning', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    /**
     * Trip Delete
     */
    #[Route(path: '/{id}/delete', name: 'app_admin_activity_delete', methods: ['POST'])]
    public function delete(Request $request, Activity $activity, ActivityRepository $activityRepository): Response
    {
        if (
            $this->isCsrfTokenValid('delete' . $activity->getId(), $request->request->get('_token'))
            // Only delete if activity is in the future
            && $activity->getDateAt() > new \DateTimeImmutable('now', new \DateTimeZone("Europe/Paris"))
        ) {
            $activityRepository->remove($activity, true);
        } else {
            $this->addFlash('danger', 'Impossible de supprimer cette activité.');
        }

        return $this->redirectToRoute('app_planning', [], Response::HTTP_SEE_OTHER);
    }
}
