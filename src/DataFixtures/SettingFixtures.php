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
        foreach (self::SETTINGS as $settingName => $settingValue) {
            $setting = (new Setting())->setName($settingName)->setIsOn($settingValue);
            $manager->persist($setting);
        }
        $manager->flush();
    }
}
