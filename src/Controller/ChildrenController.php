<?php

namespace App\Controller;

use App\Entity\Child;
use App\Form\ChildType;
use App\Repository\ChildRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/children")
 */
class ChildrenController extends AbstractController
{
    /**
     * @Route("/", name="app_children_index", methods={"GET"})
     */
    public function index(ChildRepository $childRepository): Response
    {
        return $this->render('children/index.html.twig', [
            'children' => $childRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_children_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ChildRepository $childRepository): Response
    {
        $child = new Child();
        $form = $this->createForm(ChildType::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $childRepository->add($child, true);

            return $this->redirectToRoute('app_children_index', [], Response::HTTP_SEE_OTHER);
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
        return $this->render('children/show.html.twig', [
            'child' => $child,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_children_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Child $child, ChildRepository $childRepository): Response
    {
        $form = $this->createForm(ChildType::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $childRepository->add($child, true);

            return $this->redirectToRoute('app_children_index', [], Response::HTTP_SEE_OTHER);
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
        if ($this->isCsrfTokenValid('delete'.$child->getId(), $request->request->get('_token'))) {
            $childRepository->remove($child, true);
        }

        return $this->redirectToRoute('app_children_index', [], Response::HTTP_SEE_OTHER);
    }
}
