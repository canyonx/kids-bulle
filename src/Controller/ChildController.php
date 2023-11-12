<?php

namespace App\Controller;

use App\Entity\Child;
use App\Form\ChildrenType;
use App\Repository\ChildRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/profil")
 */
class ChildController extends AbstractController
{
    /**
     * @Route("/enfant/editer/{id}", name="app_child_edit")
     */
    public function index(Child $child, Request $request, ChildRepository $childRepository): Response
    {
        $form = $this->createForm(ChildrenType::class, $child);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $childRepository->add($child, true);
            return $this->redirectToRoute('app_user');
        }

        return $this->renderForm('child/edit.html.twig', [
            'child' => $child,
            'form' => $form
        ]);
    }
}
