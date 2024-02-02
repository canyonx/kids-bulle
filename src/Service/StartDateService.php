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
        $dateStart = new \DateTimeImmutable('today');

        if (
            $date &&
            // Date user want to see is later than today or user is admin
            (($date > $dateStart->format('Y-m-d')) || $this->isGranted('ROLE_ADMIN'))
        ) {
            $dateStart = new \DateTimeImmutable($date);
        }

        return $dateStart;
    }
}
