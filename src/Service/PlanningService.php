<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;

class PlanningService
{
    private Security $security;
    private int $defaultPlanningDays;

    /**
     * Constructor
     * 
     * @param Security $security Injected Security service
     * @param int $defaultPlanningDays Default number of planning days (from service.yaml)
     */
    public function __construct(Security $security, int $defaultPlanningDays)
    {
        $this->security = $security;
        $this->defaultPlanningDays = $defaultPlanningDays;
    }

    /**
     * Return array of dates
     * Array of dates to show on a planning
     * Config duration in days in service.yaml
     * 
     * @param \DateTimeImmutable|null $fromtime Start date 
     * 
     * @return array Array of dates
     */
    public function getArrayDates(
        ?\DateTimeImmutable $fromtime
    ): array {
        // Date user wants to show
        if ($fromtime === null) {
            $fromtime = new \DateTimeImmutable('today');
        }

        $month = $fromtime->format('Y-m');
        $dayInTheMonth = (new \DateTimeImmutable('last day of ' . $month, new \DateTimeZone("Europe/Paris")))->format('d');

        $today = new \DateTimeImmutable('today');

        // If current month
        if ($today->format('Y-m') === $month && !$this->security->isGranted('ROLE_ADMIN')) {
            $dayQty = $dayInTheMonth - $today->format('d') + 1;
            // If less than 7 days, show the default planning days
            $dayQty = ($dayQty <= 7) ? $this->defaultPlanningDays : $dayQty;
        } else {
            $dayQty = $dayInTheMonth;
        }

        // Generate an array of dates
        $dates = [];
        for ($i = 0; $i < $dayQty; $i++) {
            $dates[] = new \DateTimeImmutable($fromtime->format('Y-m-d') . "+ $i day");
        }

        return $dates;
    }

    /**
     * Get a full planning
     * Organize activities by date
     *
     * @param array $activities List of activities
     * @param \DateTimeImmutable|null $dateStart Start date for the planning
     * 
     * @return array Array of activities organized by date
     */
    public function getPlanning(array $activities, \DateTimeImmutable $dateStart = null): array
    {
        // Array of dates
        $dates = $this->getArrayDates($dateStart);

        $calendar = [];
        foreach ($dates as $date) {
            $formattedDate = $date->format('Y-m-d');
            $calendar[$formattedDate] = [];

            // Add activity to the array
            foreach ($activities as $key => $activity) {
                if ($activity->getDateAt()->format('Y-m-d') === $formattedDate) {
                    $calendar[$formattedDate][$key] = $activity;
                }
            }
        }

        return $calendar;
    }
}
