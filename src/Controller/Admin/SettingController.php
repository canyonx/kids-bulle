<?php

namespace App\Controller\Admin;

use App\Entity\Setting;
use App\Form\SettingType;
use App\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin/setting')]
final class SettingController extends AbstractController
{
    #[Route('/', name: 'app_admin_setting')]
    public function index(
        SettingRepository $settingRepository,
        Request $request
    ): Response {
        $setting = $settingRepository->find(1);

        // Debugging line to check the sign code
        // dump($setting->getSignCode());

        if (!$setting) {
            // If no setting exists, create a new one with default values
            $setting = new Setting();
            $setting->setHomepageTitle('Default Title');
            $setting->setHomepageDescription('Default Description');
            $setting->setSignCode('kb31');
            $setting->setExpireAt(new \DateTimeImmutable('+1 year'));
            $settingRepository->add($setting, true);
        }

        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingRepository->add($setting, true);
            $this->addFlash('success', 'Settings updated successfully.');
            return $this->redirectToRoute('app_admin_setting');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Failed to update settings.');
        }

        return $this->render('admin/setting/index.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }
}
