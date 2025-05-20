<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Form\CopyDateType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CopyController extends AbstractController
{
    #[Route('/admin/copy', name: 'app_admin_copy')]
    public function copy(
        Request $request,
        ActivityRepository $activityRepository,
        EntityManagerInterface $em
    ): Response {
        $sourceDay = new \DateTimeImmutable($request->get('day')); //2024-01-26
        $tomorrow = new \DateTimeImmutable($request->get('day') . '+ 1 day');
        $activities = $activityRepository->findByDateBetween($sourceDay, $tomorrow);

        $targetDate = new Activity;
        $form = $this->createForm(CopyDateType::class, $targetDate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->copyActivities($activities, $targetDate, $em);
            $this->addFlash('success', 'Activités copiées avec succès');

            return $this->redirectToRoute('app_planning', [
                'date' => $targetDate->getDateAt()->format('Y-m-01'),
            ]);
        }

        return $this->render('admin/copy/index.html.twig', [
            'day' => $sourceDay,
            'form' => $form,
        ]);
    }

    /**
     * Copie les activités d'une date source vers une date cible
     *
     * @param array $sourceActivities Les activités à copier
     * @param Activity $targetDate La date cible
     * @param EntityManagerInterface $em Le gestionnaire d'entités
     */
    private function copyActivities(
        array $sourceActivities,
        Activity $targetDate,
        EntityManagerInterface $em
    ): void {
        foreach ($sourceActivities as $sourceActivity) {
            $newActivity = new Activity();
            $newDateTime = new \DateTimeImmutable(
                $targetDate->getDateAt()->format('Y-m-d') . ' ' . $sourceActivity->getDateAt()->format('H:i')
            );

            $newActivity
                ->setDateAt($newDateTime)
                ->setCategory($sourceActivity->getCategory())
                ->setTeacher($sourceActivity->getCategory()->getTeacher());

            $em->persist($newActivity);
        }

        $em->flush();
    }
}
