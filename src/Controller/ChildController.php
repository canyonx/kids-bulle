<?php

namespace App\Controller;

use App\Entity\Activity;
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
        $form = $this->createForm(ChildType::class, $child);
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
     * @Route("/{id}/planning", name="app_children_planning", methods={"GET"})
     */
    public function planning(Child $child, ActivityRepository $activityRepository, PlanningService $planningService): Response
    {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        // Child activities
        $activities = $activityRepository->findByChild($child);
        $planning = $planningService->getPlanning($activities);

        // Global planning
        $allActivities = $activityRepository->findByDateBetween();

        $reste = array_diff($allActivities, $activities);

        dump($activities);

        return $this->render('children/index.html.twig', [
            'child' => $child,
            'planning' => $planning,
            'reste' => $reste
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

        return $this->renderForm('children/edit.html.twig', [
            'child' => $child,
            'form' => $form,
            'title' => 'Edit' . $child->getFirstname()
        ]);
    }

    /**
     * TODO : remove this
     * used for child checkbox acivities
     *
     * @Route("/{id}/add/activities", name="app_children_add_activities", methods={"GET", "POST"})
     */
    public function addActivities(
        Request $request,
        Child $child,
        ChildRepository $childRepository,
        CategoryRepository $categoryRepository,
        SluggerInterface $slugger
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $form = $this->createForm(ChildType::class, $child, ['option' => 'add_activities']);
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
            $childRepository->add($child, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('children/edit.html.twig', [
            'child' => $child,
            'form' => $form,
            'title' => $child->getFirstname() . ' add activities'
        ]);
    }

    /**
     * @Route("/{id}/add/{activity}", name="app_children_add_activity", methods={"GET"})
     */
    public function addActivity(
        Child $child,
        Activity $activity,
        ChildRepository $childRepository
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $child->addActivity($activity);
        $childRepository->add($child, true);

        return $this->redirectToRoute('app_children_planning', [
            'id' => $child->getId(),
            'child' => $child
        ]);
    }

    /**
     * @Route("/{id}/remove/{activity}", name="app_children_remove_activity", methods={"GET"})
     */
    public function removeActivity(
        Child $child,
        Activity $activity,
        ChildRepository $childRepository
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        $child->addActivity($activity);
        $childRepository->add($child, true);

        return $this->redirectToRoute('app_children_planning', [
            'child' => $child
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
