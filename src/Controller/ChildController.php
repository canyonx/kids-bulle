<?php

namespace App\Controller;

use App\Entity\Child;
use App\Form\ChildType;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\ChildRepository;
use App\Service\PlanningService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        $form = $this->createForm(ChildType::class, $child, ['option' => 'new']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $child->setUser($this->getUser());
            $childRepository->add($child, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('children/new.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_children_show", methods={"GET"})
     */
    public function show(Child $child): Response
    {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        return $this->render('children/show.html.twig', [
            'child' => $child,
        ]);
    }

    /**
     * @Route("/{id}/planning", name="app_children_planning", methods={"GET"})
     */
    public function planning(Child $child, ActivityRepository $activityRepository, PlanningService $planningService): Response
    {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $activities = $activityRepository->findByChild($child);

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
        ChildRepository $childRepository,
        CategoryRepository $categoryRepository,
        SluggerInterface $slugger
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $form = $this->createForm(ChildType::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categories = $categoryRepository->findAll();
            foreach ($categories as $cat) {
                $name = strtolower($slugger->slug($cat->getName()));
                $activities = $form[$name]->getData();
                foreach ($activities as $activity) {
                    $child->addActivity($activity);
                }
            }
            // $activities = $form['activities']->getData();
            // dd($activities);
            // foreach ($activities as $activity) {
            //     $child->addActivity($activity);
            // }
            $childRepository->add($child, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('children/edit.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
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
