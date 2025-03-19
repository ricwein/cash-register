<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettingFixtures extends Fixture
{
    private const SETTINGS = [
        'landscape_mode' => true,
    ];

    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            Setting::create(
                name: 'landscape_mode',
                value: true,
                description: 'Beleg links neben der Produktauswahl anzeigen, statt darüber.'
            )
        );
        $manager->flush();
    }
}
