<?php

namespace App\Controller\Admin;

use App\Entity\Config;
use App\Form\ConfigType;
use Symfony\Component\Yaml\Yaml;
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
        EntityManagerInterface $em,
        Request $request,
        LogoUploaderService $logoUploader,
    ): Response {
        /** @var Config */
        $config = $configRepository->findAll();

        // Charger les champs depuis le fichier YAML
        $expectedFields = Yaml::parseFile($this->getParameter('kernel.project_dir') . '/config/database_config_fields.yaml')['fields'];

        // Créer les champs manquants dans la base de données
        foreach ($expectedFields as $fieldName => $defaultValue) {
            if (!$configRepository->findOneBy(['name' => $fieldName])) {
                $newConfig = new Config();
                $newConfig->setName($fieldName);
                $newConfig->setValue($defaultValue);
                $em->persist($newConfig);
                $config[] = $newConfig;
            }
        }

        $em->flush(); // Sauvegarder les nouvelles configurations si nécessaire

        // Créer le formulaire avec $config
        $form = $this->createForm(ConfigType::class, $config);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $logoFile */
            $logoFile = $form['logoFilename']->getData();

            // Enregistre les données du formulaire dans la base de données
            foreach ($config as $key => $conf) {
                // Si la configuration existe, on met à jour sa valeur
                if ($conf->getName() !== 'logoFilename') {
                    // On vérifie si la valeur est un booléen
                    if (is_bool($form[$conf->getName()]->getData())) {
                        $conf->setValue($form[$conf->getName()]->getData() ? 'true' : 'false');
                    } else {
                        $conf->setValue($form[$conf->getName()]->getData());
                    }
                }

                // Si le champ est logoFilename et qu'un fichier a été uploadé
                if ($conf->getName() === 'logoFilename' && $logoFile) {
                    // Upload du logo
                    $filename = $logoUploader->upload($logoFile);
                    $conf->setValue('uploads/' . $filename);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Configuration mise à jour.');
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
