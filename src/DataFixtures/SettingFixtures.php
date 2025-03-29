<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            Setting::create(
                name: 'landscape_mode',
                value: true,
                description: 'Beleg links neben der Artikelauswahl anzeigen, statt darüber.'
            )
        );
        $manager->persist(
            Setting::create(
                name: 'button_sound',
                value: true,
                description: 'Akustisches Feedback (Ton) für Kassen-Schaltflächen.'
            )
        );
        $manager->flush();
    }
}
