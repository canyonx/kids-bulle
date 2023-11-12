<?php

namespace App\Controller;

use App\Entity\Child;
use App\Form\ChildrenType;
use App\Repository\ChildRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil", name="app_user")
     */
    public function index(Request $request, ChildRepository $childRepository): Response
    {
        $child = new Child;

        $form = $this->createForm(ChildrenType::class, $child);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $child->setUser($this->getUser());
            $childRepository->add($child, true);
            return $this->redirectToRoute('app_user');
        }

        return $this->renderForm('user/index.html.twig', [
            'user' => $this->getUser(),
            'form' => $form
        ]);
    }
}
