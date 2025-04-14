<?php

namespace App\Controller\Admin;

use App\Entity\Config;
use App\Form\ConfigType;
use App\DataFixtures\ConfigFixtures;
use App\Repository\ConfigRepository;
use App\Service\LogoUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin/config')]
final class ConfigController extends AbstractController
{
    #[Route('/', name: 'app_admin_config')]
    public function index(
        ConfigRepository $configRepository,
        ConfigFixtures $configFixtures,
        EntityManagerInterface $em,
        Request $request,
        LogoUploaderService $logoUploader,
    ): Response {
        /** @var Config */
        $config = $configRepository->findAll();

        if (!$config) {
            // If no setting exists, create a new one with default values
            $configFixtures->createConfig();
        }

        $form = $this->createForm(ConfigType::class, $config);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $logoFile */
            $logoFile = $form['logoFilename']->getData();

            // Enregistre les données du formulaire dans la base de données
            foreach ($config as $key => $conf) {
                // // Si la configuration n'existe pas, on la crée
                // if (!$configRepository->findOneBy(['name' => $key])) {
                //     $c = new Config();
                //     $c->setName($key);
                //     $c->setValue($conf);
                //     $configRepository->add($c, true);
                //     continue;
                // }

                // Si la configuration existe, on met à jour sa valeur
                if ($conf->getName() !== 'logoFilename') {
                    $conf->setValue($form[$conf->getName()]->getData());
                }

                // Si le champ est logoFilename et qu'un fichier a été uploadé
                if ($conf->getName() === 'logoFilename' && $logoFile) {
                    // Upload du logo
                    $filename = $logoUploader->upload($logoFile);
                    $conf->setValue('uploads/' . $filename);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Configuration mise à jour avec succès !');
            return $this->redirectToRoute('app_admin_config');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Erreur dans le formulaire !');
        }

        return $this->render('admin/config/index.html.twig', [
            'form' => $form,
        ]);
    }
}
