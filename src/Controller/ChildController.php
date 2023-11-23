<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Child;
use App\Form\ChildType;
use App\Repository\ActivityRepository;
use App\Repository\ChildRepository;
use App\Service\PlanningService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/profil/children")
 */
class ChildController extends AbstractController
{
    /**
     * @Route("/new", name="app_children_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ChildRepository $childRepository): Response
    {
        $child = new Child();
        $form = $this->createForm(ChildType::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $child->setUser($this->getUser());
            $childRepository->add($child, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->renderForm('children/new.html.twig', [
            'child' => $child,
            'form' => $form,
        ], $response);
    }

    /**
     * @Route("/{id}/planning", name="app_children_planning", methods={"GET"})
     */
    public function planning(Child $child, ActivityRepository $activityRepository, PlanningService $planningService): Response
    {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        // Child activities
        $activities = $activityRepository->findByDateBetween();
        $planning = $planningService->getPlanning($activities);

        return $this->render('children/index.html.twig', [
            'child' => $child,
            'planning' => $planning
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_children_edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        Child $child,
        ChildRepository $childRepository
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $form = $this->createForm(ChildType::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $childRepository->add($child, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->renderForm('children/edit.html.twig', [
            'child' => $child,
            'form' => $form,
            'title' => 'Edit' . $child->getFirstname()
        ], $response);
    }

    /**
     * @Route("/{id}/{action}/{activity}", name="app_children_action_activity", methods={"GET"})
     */
    public function actionActivity(
        Child $child,
        Activity $activity,
        string $action,
        ChildRepository $childRepository,
        Request $request
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        if ($action === 'remove') {
            $child->removeActivity($activity);
        } elseif ($action === 'add') {
            $child->addActivity($activity);
        }

        $childRepository->add($child, true);

        return $this->redirect($request->headers->get('referer'), Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="app_children_delete", methods={"POST"})
     */
    public function delete(Request $request, Child $child, ChildRepository $childRepository): Response
    {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        if ($this->isCsrfTokenValid('delete' . $child->getId(), $request->request->get('_token'))) {
            $childRepository->remove($child, true);
        }

        return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
    }
}
