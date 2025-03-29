<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const array CATEGORIES = [
        ['Heißgetränke', '#B71C1C'],
        ['AfG', '#B39DDB'],
        ['Eisgetränke', '#283593'],
        ['Bier / Wein / Sekt', '#FBC02D'],
        ['Pfand', '#FFF8E1'],
        ['Eis', '#4FC3F7'],
        ['Speisen', '#9E9D24'],
        ['Snacks', '#00E676'],
        ['Kuchen', '#FF80AB'],
        ['Grill', '#795548'],
        ['Tabak etc.', '#304FFE'],
        ['Sonstiges', '#B0BEC5'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $category) {
            [$name, $color] = $category;
            $manager->persist((new Category())->setName($name)->setColor($color));
        }
        $manager->flush();
    }
}
