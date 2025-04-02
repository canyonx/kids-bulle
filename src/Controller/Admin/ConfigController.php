<?php

namespace App\Controller\Admin;

use App\DataFixtures\ConfigFixtures;
use App\Entity\Config;
use App\Form\ConfigType;
use App\Repository\ConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin/config')]
final class ConfigController extends AbstractController
{
    #[Route('/', name: 'app_admin_config')]
    public function index(
        ConfigRepository $configRepository,
        ConfigFixtures $configFixtures,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        /** @var Config */
        $config = $configRepository->findAll();

        // Debugging line to check the sign code
        // dump($config);

        if (!$config) {
            // If no setting exists, create a new one with default values
            $configFixtures->createConfig();
        }

        $form = $this->createForm(ConfigType::class, $config);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Enregistre les données du formulaire dans la base de données
            foreach ($config as $key => $value) {
                $value->setValue($form[$value->getName()]->getData());
                $em->persist($value);
            }
            $em->flush();
            // $configRepository->add($config, true);
            $this->addFlash('success', 'Mis à jour avec succès !');
            return $this->redirectToRoute('app_admin_config');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Erreur dans le formulaire !');
        }

        return $this->render('admin/config/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
