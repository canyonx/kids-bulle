<?php

namespace App\Controller\Admin;

use App\Repository\ChildRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChildController extends AbstractController
{
    /**
     * @Route("/admin/child", name="app_admin_child_index")
     */
    public function index(ChildRepository $childRepository): Response
    {
        return $this->render('admin/child/index.html.twig', [
            'childs' => $childRepository->findBy([], ['lastname' => 'ASC']),
        ]);
    }
}
