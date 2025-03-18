<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    private const PRODUCTS = [
        // Tagesbetrieb:
        [1, 'Getränke', 'Fassbrause', 2.99, 'fa-solid fa-glass-water', null],
        [1, 'Getränke', 'Fanta', 2.99, 'fa-solid fa-glass-water', null],
        [1, 'Getränke', 'Sprite', 2.99, 'fa-solid fa-glass-water', null],

        [1, 'Grill', 'Steak', 3.49, 'fa-solid fa-drumstick-bite', null],
        [1, 'Grill', 'Bratwurst', 2.99, 'fa-solid fa-hotdog', null],
        [1, 'Grill', 'Veg. Bratling', 3.49, 'fa-solid fa-cookie', null],
        [1, 'Grill', 'Brezel', 2.49, 'fa-solid fa-cookie-bite', null],

        [1, 'Snacks', 'Kuchen', 2.49, 'fa-solid fa-cake-candles', null],

        [1, 'Eis', 'Magnum', 2.49, 'fa-solid fa-ice-cream', null],
        [1, 'Eis', 'Cornetto', 2.49, 'fa-solid fa-ice-cream', null],
        [1, 'Eis', 'Solero', 2.49, 'fa-solid fa-ice-cream', null],
        [1, 'Eis', 'Nogger', 1.99, 'fa-solid fa-ice-cream', null],
        [1, 'Eis', 'Kaktus', 1.99, 'fa-solid fa-ice-cream', null],
        [1, 'Eis', 'Flutschfinger', 1.99, 'fa-solid fa-ice-cream', null],
        [1, 'Eis', 'Oreo', 2.99, 'fa-solid fa-ice-cream', null],
        [1, 'Eis', 'Caretta', 1.49, 'fa-solid fa-ice-cream', null],

        // Veranstaltung:
        [2, 'Pfand', 'Ausgabe', 1.00, 'fa-solid fa-money-bill', null],
        [2, 'Pfand', 'Rückgabe', -1.00, 'fa-solid fa-money-bill', null],

        [2, 'Getränke', 'Fassbrause', 3.99, 'fa-solid fa-glass-water', null],
        [2, 'Getränke', 'Fanta', 3.99, 'fa-solid fa-glass-water', null],
        [2, 'Getränke', 'Sprite', 3.99, 'fa-solid fa-glass-water', null],

        [2, 'Grill', 'Steak', 4.49, 'fa-solid fa-drumstick-bite', null],
        [2, 'Grill', 'Bratwurst', 3.99, 'fa-solid fa-hotdog', null],
        [2, 'Grill', 'Veg. Bratling', 4.49, 'fa-solid fa-cookie', null],
        [2, 'Grill', 'Brezel', 4.49, 'fa-solid fa-cookie-bite', null],
    ];

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $events = [];
        foreach ($this->eventRepository->findAll() as $event) {
            $events[$event->getId()] = $event;
        }
        $categories = [];
        foreach ($this->categoryRepository->findAll() as $category) {
            $categories[$category->getName()] = $category;
        }

        foreach (self::PRODUCTS as $data) {
            [$eventId, $categoryName, $name, $price, $icon, $color] = $data;
            $product = new Product();
            $product->setEvent($events[$eventId]);
            $product->setCategory($categories[$categoryName]);
            $product->setName($name);
            $product->setPrice($price);
            $product->setIcon($icon);
            $product->setColor($color);

            $manager->persist($product);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [EventFixtures::class, CategoryFixtures::class];
    }
}
