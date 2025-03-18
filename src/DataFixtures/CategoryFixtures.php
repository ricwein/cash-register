<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const CATEGORIES = [
        ['Pfand', '#FFF8E1'],
        ['Getränke', '#FFD54F'],
        ['Grill', '#795548'],
        ['Snacks', '#FF8A65'],
        ['Eis', '#80DEEA'],
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
