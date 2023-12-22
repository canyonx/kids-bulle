<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\HttpFoundation\ParameterBag;

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
        if ($fromtime === null) {
            $fromtime = new \DateTimeImmutable('today');
        }

        for ($i = 0; $i < $this->getParameter('app.planning_days'); $i++) {
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
