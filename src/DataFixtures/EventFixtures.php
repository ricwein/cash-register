<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Sale;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist((new Event())->setName('Tagesbetrieb'));
        $manager->persist((new Event())->setName('Veranstaltung'));
        $manager->flush();
    }
}
