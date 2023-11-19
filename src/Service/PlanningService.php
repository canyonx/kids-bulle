<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\HttpFoundation\ParameterBag;

class PlanningService extends AbstractController
{
    /**
     * Array of dates to show on a planning
     * Confid duration in days in service.yaml
     *
     * @return array
     */
    public function getArrayDates(): array
    {
        $dates = [];
        for ($i = 0; $i < $this->getParameter('app.planning_days'); $i++) {
            $dates[] = new \DateTimeImmutable("today + $i days");
        }
        return $dates;
    }
}
