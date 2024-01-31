<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StartDateService extends AbstractController
{
    /**
     * Return start date for planning
     *
     * @param string|null $date
     * @return \DateTimeImmutable
     */
    public function getStartDate(string $date = null): \DateTimeImmutable
    {
        // Set correct start date
        $today = (new \DateTimeImmutable('today', new \DateTimeZone("Europe/Paris")))->format('Y-m-d');

        if ($date && (($date > $today) || $this->isGranted('ROLE_ADMIN'))) {
            $dateStart = new \DateTimeImmutable($date, new \DateTimeZone("Europe/Paris"));
        } else {
            $dateStart = new \DateTimeImmutable('today', new \DateTimeZone("Europe/Paris"));
        }

        return $dateStart;
    }
}
