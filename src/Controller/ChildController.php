<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Child;
use App\Form\ChildType;
use App\Repository\ActivityRepository;
use App\Repository\ChildRepository;
use App\Service\PlanningService;
use App\Service\StartDateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/profil/children')]
class ChildController extends AbstractController
{
    #[Route(path: '/new', name: 'app_children_new', methods: ['GET', 'POST'])]
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

        return $this->render('children/new.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    #[Route(path: '/{id}/planning', name: 'app_children_planning', methods: ['GET'])]
    public function planning(
        Child $child,
        ActivityRepository $activityRepository,
        PlanningService $planningService,
        StartDateService $startDateService,
        Request $request
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $dateStart = $startDateService->getStartDate($request->get('date'));

        // Child activities
        $activities = $activityRepository->findByDateBetween($dateStart);
        $planning = $planningService->getPlanning($activities, $dateStart);

        return $this->render('children/index.html.twig', [
            'child' => $child,
            'planning' => $planning,
            'date' => $request->get('date')
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'app_children_edit', methods: ['GET', 'POST'])]
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

        return $this->render('children/edit.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    #[Route(path: '/{id}', name: 'app_children_delete', methods: ['POST'])]
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
