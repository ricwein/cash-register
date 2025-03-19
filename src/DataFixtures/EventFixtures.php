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
        $manager->persist((new Event())->setName('Tagesbetrieb')->setUseCategoryTabs(true));
        $manager->persist((new Event())->setName('[Veranstaltungen] - Grill')->setUseCategoryTabs(false));
        $manager->persist((new Event())->setName('[Veranstaltungen] - Kiosk')->setUseCategoryTabs(false));
        $manager->persist((new Event())->setName('[Veranstaltungen] - Neubau')->setUseCategoryTabs(false));
        $manager->persist((new Event())->setName('[Veranstaltungen] - Chalet')->setUseCategoryTabs(false));
        $manager->flush();
    }
}
