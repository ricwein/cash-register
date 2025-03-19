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
        ['Tagesbetrieb', 'Eisgetränke', 'Fassbrause', 2.99, 'fa-solid fa-glass-water', null],
        ['Tagesbetrieb', 'Eisgetränke', 'Fanta', 2.99, 'fa-solid fa-glass-water', null],
        ['Tagesbetrieb', 'Eisgetränke', 'Sprite', 2.99, 'fa-solid fa-glass-water', null],

        ['Tagesbetrieb', 'Grill', 'Steak', 3.49, 'fa-solid fa-drumstick-bite', null],
        ['Tagesbetrieb', 'Grill', 'Bratwurst', 2.99, 'fa-solid fa-hotdog', null],
        ['Tagesbetrieb', 'Grill', 'Veg. Bratling', 3.49, 'fa-solid fa-cookie', null],
        ['Tagesbetrieb', 'Grill', 'Butterbrezel', 2.49, 'fa-solid fa-cookie-bite', null],

        ['Tagesbetrieb', 'Kuchen', 'Kirchkuchen', 3.49, 'fa-solid fa-cake-candles', null],
        ['Tagesbetrieb', 'Kuchen', 'Erdbeertorte', 3.49, 'fa-solid fa-cake-candles', null],

        ['Tagesbetrieb', 'Eis', 'Magnum', 2.49, 'fa-solid fa-ice-cream', null],
        ['Tagesbetrieb', 'Eis', 'Cornetto', 2.49, 'fa-solid fa-ice-cream', null],
        ['Tagesbetrieb', 'Eis', 'Solero', 2.49, 'fa-solid fa-ice-cream', null],
        ['Tagesbetrieb', 'Eis', 'Nogger', 1.99, 'fa-solid fa-ice-cream', null],
        ['Tagesbetrieb', 'Eis', 'Kaktus', 1.99, 'fa-solid fa-ice-cream', null],
        ['Tagesbetrieb', 'Eis', 'Flutschfinger', 1.99, 'fa-solid fa-ice-cream', null],
        ['Tagesbetrieb', 'Eis', 'Oreo', 2.99, 'fa-solid fa-ice-cream', null],
        ['Tagesbetrieb', 'Eis', 'Caretta', 1.49, 'fa-solid fa-ice-cream', null],

        // Veranstaltung:
        ['[Veranstaltungen] - Grill', 'Grill', 'Steak', 4.49, 'fa-solid fa-drumstick-bite', null],
        ['[Veranstaltungen] - Grill', 'Grill', 'Bratwurst', 3.99, 'fa-solid fa-hotdog', null],
        ['[Veranstaltungen] - Grill', 'Grill', 'Veg. Bratling', 4.49, 'fa-solid fa-cookie', null],
        ['[Veranstaltungen] - Grill', 'Grill', 'Butterbrezel', 4.49, 'fa-solid fa-cookie-bite', null],

        ['[Veranstaltungen] - Kiosk', 'Eisgetränke', 'Ausgabe', 1.00, 'fa-solid fa-money-bill', '#FFF8E1'],
        ['[Veranstaltungen] - Kiosk', 'Eisgetränke', 'Rückgabe', -1.00, 'fa-solid fa-money-bill', '#FFF8E1'],
        ['[Veranstaltungen] - Kiosk', 'Eisgetränke', 'Fassbrause', 3.99, 'fa-solid fa-glass-water', null],
        ['[Veranstaltungen] - Kiosk', 'Eisgetränke', 'Fanta', 3.99, 'fa-solid fa-glass-water', null],
        ['[Veranstaltungen] - Kiosk', 'Eisgetränke', 'Sprite', 3.99, 'fa-solid fa-glass-water', null],

        ['[Veranstaltungen] - Neubau', 'Eisgetränke', 'Ausgabe', 1.00, 'fa-solid fa-money-bill', '#FFF8E1'],
        ['[Veranstaltungen] - Neubau', 'Eisgetränke', 'Rückgabe', -1.00, 'fa-solid fa-money-bill', '#FFF8E1'],
        ['[Veranstaltungen] - Neubau', 'Eisgetränke', 'Fassbrause', 3.99, 'fa-solid fa-glass-water', null],
        ['[Veranstaltungen] - Neubau', 'Eisgetränke', 'Fanta', 3.99, 'fa-solid fa-glass-water', null],
        ['[Veranstaltungen] - Neubau', 'Eisgetränke', 'Sprite', 3.99, 'fa-solid fa-glass-water', null],

        ['[Veranstaltungen] - Chalet', 'Eisgetränke', 'Ausgabe', 1.00, 'fa-solid fa-money-bill', '#FFF8E1'],
        ['[Veranstaltungen] - Chalet', 'Eisgetränke', 'Rückgabe', -1.00, 'fa-solid fa-money-bill', '#FFF8E1'],
        ['[Veranstaltungen] - Chalet', 'Eisgetränke', 'Fassbrause', 3.99, 'fa-solid fa-glass-water', null],
        ['[Veranstaltungen] - Chalet', 'Eisgetränke', 'Fanta', 3.99, 'fa-solid fa-glass-water', null],
        ['[Veranstaltungen] - Chalet', 'Eisgetränke', 'Sprite', 3.99, 'fa-solid fa-glass-water', null],

    ];

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $events = [];
        foreach ($this->eventRepository->findAll() as $event) {
            $events[$event->getName()] = $event;
        }
        $categories = [];
        foreach ($this->categoryRepository->findAll() as $category) {
            $categories[$category->getName()] = $category;
        }

        foreach (self::PRODUCTS as $data) {
            [$eventName, $categoryName, $name, $price, $icon, $color] = $data;
            $product = new Product();
            $product->setEvent($events[$eventName]);
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
