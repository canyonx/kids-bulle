<?php

namespace App\Twig\Components;

use App\Entity\Activity;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class ActivityCard
{
    public Activity $activity;
}
