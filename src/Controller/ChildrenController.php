<?php

namespace App\Controller;

use App\Entity\Child;
use App\Form\ChildType;
use App\Repository\ChildRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/profil/children")
 */
class ChildrenController extends AbstractController
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
     * @Route("/{id}/edit", name="app_children_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Child $child, ChildRepository $childRepository): Response
    {
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
