<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlanningService extends AbstractController
{
    /**
     * Return array of dates
     * Array of dates to show on a planning
     * Confid duration in days in service.yaml
     * 
     * @param fromtime DateTime, start date 
     * 
     * @return dates[] Array
     */
    public function getArrayDates(
        \DateTimeImmutable $fromtime = null
    ): array {
        // Date user want to show
        if ($fromtime === null) $fromtime = new \DateTimeImmutable('today');
        $month = $fromtime->format('Y-m');
        $dayInTheMonth = (new \DateTimeImmutable('last day of ' . $month, new \DateTimeZone("Europe/Paris")))->format('d');

        $today = new \DateTimeImmutable('today');

        // If current month
        if ($today->format('Y-m') == $month && !$this->isGranted('ROLE_ADMIN')) {
            $dayQty = $dayInTheMonth - $today->format('d') + 1;
            // If less than 7 days show 31 days
            $dayQty = ($dayQty <= 7) ? $this->getParameter('app.planning_days') : $dayQty;
        } else {
            $dayQty = $dayInTheMonth;
        }

        // $dayQty = $dayInTheMonth;

        for ($i = 0; $i < $dayQty; $i++) {
            $dates[] = new \DateTimeImmutable($fromtime->format('Y-m-d') . "+ $i day");
        }
        // dd($dates);
        return $dates;
    }

    /**
     * Get a full planning
     *
     * @param array $activities
     * @return array
     */
    public function getPlanning(array $activities, \DateTimeImmutable $dateStart = null): array
    {
        // Array of dates
        $dates = $this->getArrayDates($dateStart);

        $calendar = [];
        foreach ($dates as $date) {
            $date = $date->format('Y-m-d');
            $calendar[$date] = [];

            // Add activity to Array
            foreach ($activities as $key => $activity) {
                if ($activity->getDateAt()->format('Y-m-d') == $date) {
                    $calendar[$date][$key] = $activity;
                }
            }
        }
        // dd($calendar);
        return $calendar;
    }
}
