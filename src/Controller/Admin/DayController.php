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

class DayController extends AbstractController
{
    #[Route('/day/copy', name: 'app_admin_day_copy')]
    public function copy(Request $request, ActivityRepository $activityRepository, EntityManagerInterface $em): Response
    {
        $day = new \DateTimeImmutable($request->get('day')); //2024-01-26
        $tomorrow = new \DateTimeImmutable($request->get('day') . '+ 1 day');
        $activities = $activityRepository->findByDateBetween($day, $tomorrow);

        $newDate = new Activity;
        $form = $this->createForm(CopyDateType::class, $newDate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($activities as $a) {
                $activity = new Activity;
                $copyTo = $newDate->getDateAt();
                $copyTo = new \DateTimeImmutable($copyTo->format('Y-m-d 9:00'));
                $activity->setDateAt($copyTo)
                    ->setCategory($a->getCategory())
                    ->setTeacher($a->getCategory()->getTeacher());
                $em->persist($activity);
            }
            $em->flush();
            return $this->redirectToRoute('app_admin_activity_index');
        }

        return $this->render('admin/day/index.html.twig', [
            'day' => $day,
            'form' => $form,
        ]);
    }
}
