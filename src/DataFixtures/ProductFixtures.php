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
        ['Tagesbetrieb', 'Heißgetränke', 'Filterkaffee klein', 2.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Filterkaffee groß', 3.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Kaffee crème klein', 3.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Kaffee crème groß', 4.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Cappuccino klein', 3.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Cappuccino groß', 4.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Milchkaffee klein', 3.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Milchkaffee groß', 4.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Latte macchiato', 4.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Espresso klein', 2.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Espresso groß', 3.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Schokokaffee klein', 3.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Schokokaffee groß', 4.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Heiße Schokolade klein', 3.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Heiße Schokolade groß', 4.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Tee', 3.00, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Glühwein', 3.50, 'fa-solid fa-mug-hot'],
        ['Tagesbetrieb', 'Heißgetränke', 'Kinderpunsch', 2.50, 'fa-solid fa-mug-hot'],

        ['Tagesbetrieb', 'Pfand', 'Tragepappe', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['Tagesbetrieb', 'Pfand', 'Tragepappe zurück', -1.00, 'fa-solid fa-money-bill-transfer'],

        ['Tagesbetrieb', 'AfG', 'Wasser klein', 2.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Wasser groß', 3.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Fassbrause klein', 2.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Fassbrause groß', 3.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Cola / Limo klein', 2.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Cola / Limo groß', 3.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Saft / Saftschorle klein', 3.00, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Saft / Saftschorle groß', 4.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Club Mate', 3.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Fritz Limo / Cola 0,33l', 3.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Bio Elsterschorle 0,33l', 3.50, 'fa-solid fa-glass-water'],
        ['Tagesbetrieb', 'AfG', 'Rauch Saft 0,33l', 3.50, 'fa-solid fa-glass-water'],

        ['Tagesbetrieb', 'Pfand', 'Pfand Ausgabe', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['Tagesbetrieb', 'Pfand', 'Pfandbecher zurück', -1.00, 'fa-solid fa-money-bill-transfer'],
        ['Tagesbetrieb', 'Pfand', 'Tragepappe', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['Tagesbetrieb', 'Pfand', 'Tragepappe zurück', -1.00, 'fa-solid fa-money-bill-transfer'],

        ['Tagesbetrieb', 'Eisgetränke', 'Eisschokolade', 5.00, 'fa-solid fa-whiskey-glass'],
        ['Tagesbetrieb', 'Eisgetränke', 'Eiskaffee', 5.00, 'fa-solid fa-whiskey-glass'],
        ['Tagesbetrieb', 'Eisgetränke', 'Kugel Eis', 1.50, 'fa-solid fa-whiskey-glass'],

        ['Tagesbetrieb', 'Bier / Wein / Sekt', 'Fassbier / Biermix 0,4l', 4.50, 'fa-solid fa-beer-mug-empty'],
        ['Tagesbetrieb', 'Bier / Wein / Sekt', 'Flaschenbier 0,5l', 4.50, 'fa-solid fa-beer-mug-empty'],
        ['Tagesbetrieb', 'Bier / Wein / Sekt', 'Wein / Weinschorle 0,2l', 4.50, 'fa-solid fa-wine-glass'],
        ['Tagesbetrieb', 'Bier / Wein / Sekt', 'Wein / Weinschorle 0,4l', 8.00, 'fa-solid fa-wine-glass'],
        ['Tagesbetrieb', 'Bier / Wein / Sekt', 'Sekt 0,2l', 4.50, 'fa-solid fa-champagne-glasses'],
        ['Tagesbetrieb', 'Bier / Wein / Sekt', 'Sekt 0,4l', 8.00, 'fa-solid fa-champagne-glasses'],

        ['Tagesbetrieb', 'Pfand', 'Tragepappe', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['Tagesbetrieb', 'Pfand', 'Tragepappe zurück', -1.00, 'fa-solid fa-money-bill-transfer'],

        ['Tagesbetrieb', 'Eis', 'Eis', 1.00, 'fa-solid fa-ice-cream'],
        ['Tagesbetrieb', 'Eis', 'Eis', 1.50, 'fa-solid fa-ice-cream'],
        ['Tagesbetrieb', 'Eis', 'Eis', 1.50, 'fa-solid fa-ice-cream'],
        ['Tagesbetrieb', 'Eis', 'Eis', 2.00, 'fa-solid fa-ice-cream'],
        ['Tagesbetrieb', 'Eis', 'Eis', 2.00, 'fa-solid fa-ice-cream'],
        ['Tagesbetrieb', 'Eis', 'Eis', 2.00, 'fa-solid fa-ice-cream'],
        ['Tagesbetrieb', 'Eis', 'Eis', 3.50, 'fa-solid fa-ice-cream'],
        ['Tagesbetrieb', 'Eis', 'Eis', 4.00, 'fa-solid fa-ice-cream'],
        ['Tagesbetrieb', 'Eis', 'Eis', 4.50, 'fa-solid fa-ice-cream'],

        ['Tagesbetrieb', 'Speisen', 'Butterbrezel', 3.00, 'fa-solid fa-wheat-awn'],
        ['Tagesbetrieb', 'Speisen', 'Bockwurst', 3.50, 'fa-solid fa-hotdog'],
        ['Tagesbetrieb', 'Speisen', 'Wiener', 4.00, 'fa-solid fa-hotdog'],
        ['Tagesbetrieb', 'Speisen', 'Currywurst', 4.50, 'fa-solid fa-hotdog'],
        ['Tagesbetrieb', 'Speisen', 'Brötchen', 0, 50, 'fa-solid fa-bread-slice'],

        ['Tagesbetrieb', 'Snacks', 'Chips', 2.50, 'fa-solid fa-cookie-bite'],
        ['Tagesbetrieb', 'Snacks', 'Nüsse', 2.00, 'fa-solid fa-cookie-bite'],
        ['Tagesbetrieb', 'Snacks', 'Lollies', 0, 50, 'fa-solid fa-cookie-bite'],
        ['Tagesbetrieb', 'Snacks', 'Pick up ', 1.00, 'fa-solid fa-cookie-bite'],
        ['Tagesbetrieb', 'Snacks', 'Ballisto', 1.50, 'fa-solid fa-cookie-bite'],
        ['Tagesbetrieb', 'Snacks', 'Gummibärchen', 0, 50, 'fa-solid fa-cookie-bite'],
        ['Tagesbetrieb', 'Snacks', 'Marshmallows 3 Stück', 1.50, 'fa-solid fa-cookie-bite'],

        ['Tagesbetrieb', 'Kuchen', 'Kuchen', 2.50, 'fa-solid fa-cake-candles'],
        ['Tagesbetrieb', 'Kuchen', 'Kuchen', 3.00, 'fa-solid fa-cake-candles'],
        ['Tagesbetrieb', 'Kuchen', 'Kuchen', 3.50, 'fa-solid fa-cake-candles'],
        ['Tagesbetrieb', 'Kuchen', 'Kuchen', 4.00, 'fa-solid fa-cake-candles'],

        ['Tagesbetrieb', 'Speisen', 'Butterbrezel', 3.00, 'fa-solid fa-wheat-awn'],
        ['Tagesbetrieb', 'Grill', 'Bratwurst', 3.50, 'fa-solid fa-hotdog'],
        ['Tagesbetrieb', 'Grill', 'Steak', 4.50, 'fa-solid fa-utensils'],
        ['Tagesbetrieb', 'Grill', 'Gemüsefrikadelle', 3.00, 'fa-solid fa-utensils'],
        ['Tagesbetrieb', 'Grill', 'Blumenkohl-Käse-Medaillon', 3.50, 'fa-solid fa-utensils'],
        ['Tagesbetrieb', 'Grill', 'Gemüseschnitzel', 3.50, 'fa-solid fa-utensils'],
        ['Tagesbetrieb', 'Speisen', 'Brötchen', 0, 50, 'fa-solid fa-bread-slice'],
        ['Tagesbetrieb', 'Grill', 'Bratwurst ohne Brötchen', 3.00, 'fa-solid fa-hotdog'],
        ['Tagesbetrieb', 'Grill', 'Steak ohne Brötchen', 4.00, 'fa-solid fa-utensils'],
        ['Tagesbetrieb', 'Grill', 'Gemüsefrikadelle ohne Brötchen', 2.50, 'fa-solid fa-utensils'],
        ['Tagesbetrieb', 'Grill', 'Blumenkohl-Käse-Medaillon ohne Brötchen', 3.00, 'fa-solid fa-utensils'],
        ['Tagesbetrieb', 'Grill', 'Gemüseschnitzel ohne Brötchen', 3.00, 'fa-solid fa-utensils'],

        ['Tagesbetrieb', 'Tabak etc.', 'Filter', 1.50, 'fa-solid fa-smoking'],
        ['Tagesbetrieb', 'Tabak etc.', 'GIZEH Papers', 1.00, 'fa-solid fa-smoking'],
        ['Tagesbetrieb', 'Tabak etc.', 'GIZEH Paper doppelt', 1.50, 'fa-solid fa-smoking'],
        ['Tagesbetrieb', 'Tabak etc.', 'Pueblo', 6.50, 'fa-solid fa-smoking'],

        ['[Veranstaltungen] - Grill', 'Speisen', 'Butterbrezel ', 3.50, 'fa-solid fa-wheat-awn'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Bratwurst ', 4.00, 'fa-solid fa-hotdog'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Steak ', 5.00, 'fa-solid fa-utensils'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Gemüsefrikadelle ', 3.50, 'fa-solid fa-utensils'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Blumenkohl-Käse-Medaillon', 4.00, 'fa-solid fa-utensils'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Gemüseschnitzel', 4.50, 'fa-solid fa-utensils'],
        ['[Veranstaltungen] - Grill', 'Speisen', 'Brötchen ', 0, 50, 'fa-solid fa-bread-slice'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Bratwurst ohne Brötchen ', 3.50, 'fa-solid fa-hotdog'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Steak ohne Brötchen ', 4.50, 'fa-solid fa-utensils'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Gemüsefrikadelle ohne Brötchen ', 3.00, 'fa-solid fa-utensils'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Blumenkohl-Käse-Medaillon ohne Brötchen ', 3.50, 'fa-solid fa-utensils'],
        ['[Veranstaltungen] - Grill', 'Grill', 'Gemüseschnitzel ohne Brötchen ', 4.00, 'fa-solid fa-utensils'],
        ['[Veranstaltungen] - Grill', 'Snacks', 'Marshmallows 3 Stück', 1.50, 'fa-solid fa-utensils'],

        ['[Veranstaltungen] - Kiosk', 'Bier / Wein / Sekt', 'Fassbier / Biermix 0,4l', 5.00, 'fa-solid fa-beer-mug-empty'],
        ['[Veranstaltungen] - Kiosk', 'Bier / Wein / Sekt', 'Wein / Schorle / Sekt 0,2l', 5.00, 'fa-solid fa-wine-glass'],
        ['[Veranstaltungen] - Kiosk', 'Bier / Wein / Sekt', 'Wein / Schorle / Sekt 0,4l', 9.00, 'fa-solid fa-wine-glass'],
        ['[Veranstaltungen] - Kiosk', 'AfG', 'Fassbrause 0,4l', 4.50, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Kiosk', 'AfG', 'Cola / Limo 0,4l', 4.50, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Kiosk', 'AfG', 'Saft / Schorle 0,4l', 5.00, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Kiosk', 'AfG', 'Wasser 0,4l', 4.00, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Kiosk', 'Heißgetränke', 'Filterkaffee', 2.50, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Kiosk', 'Heißgetränke', 'Tee', 3.00, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Kiosk', 'Speisen', 'Butterbrezel', 3.50, 'fa-solid fa-wheat-awn'],
        ['[Veranstaltungen] - Kiosk', 'Heißgetränke', 'Glühwein', 4.00, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Kiosk', 'Heißgetränke', 'Kinderpunsch', 3.00, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Kiosk', 'Pfand', 'Pfandbecher', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Kiosk', 'Pfand', 'Pfandbecher zurück', -1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Kiosk', 'Pfand', 'Tragepappe', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Kiosk', 'Pfand', 'Tragepappe zurück', -1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Kiosk', 'Sonstiges', 'Regencape', 2.50, 'fa-solid fa-umbrella'],
        ['[Veranstaltungen] - Kiosk', 'Tabak etc.', 'Filter', 1.50, 'fa-solid fa-smoking'],
        ['[Veranstaltungen] - Kiosk', 'Tabak etc.', 'GIZEH Papers', 1.00, 'fa-solid fa-smoking'],
        ['[Veranstaltungen] - Kiosk', 'Tabak etc.', 'GIZEH Paper doppelt', 1.50, 'fa-solid fa-smoking'],
        ['[Veranstaltungen] - Kiosk', 'Tabak etc.', 'Pueblo', 6.50, 'fa-solid fa-smoking'],

        ['[Veranstaltungen] - Neubau', 'Bier / Wein / Sekt', 'Fassbier / Biermix 0,4l', 5.00, 'fa-solid fa-beer-mug-empty'],
        ['[Veranstaltungen] - Neubau', 'Bier / Wein / Sekt', 'Wein / Schorle / Sekt 0,2l', 5.00, 'fa-solid fa-wine-glass'],
        ['[Veranstaltungen] - Neubau', 'Bier / Wein / Sekt', 'Wein / Schorle / Sekt 0,4l', 9.00, 'fa-solid fa-wine-glass'],
        ['[Veranstaltungen] - Neubau', 'AfG', 'Fassbrause 0,4l', 4.50, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Neubau', 'AfG', 'Cola / Limo 0,4l', 4.50, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Neubau', 'AfG', 'Saft / Schorle 0,4l', 5.00, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Neubau', 'AfG', 'Wasser 0,4l', 4.00, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Neubau', 'Heißgetränke', 'Filterkaffee', 2.50, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Neubau', 'Speisen', 'Butterbrezel', 3.50, 'fa-solid fa-wheat-awn'],
        ['[Veranstaltungen] - Neubau', 'Snacks', 'Chips', 2.50, 'fa-solid fa-cookie-bite'],
        ['[Veranstaltungen] - Neubau', 'Snacks', 'Nüsse', 2.00, 'fa-solid fa-cookie-bite'],
        ['[Veranstaltungen] - Neubau', 'Heißgetränke', 'Glühwein', 4.00, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Neubau', 'Heißgetränke', 'Kinderpunsch', 3.00, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Neubau', 'Pfand', 'Pfandbecher', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Neubau', 'Pfand', 'Pfandbecher zurück', -1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Neubau', 'Pfand', 'Tragepappe', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Neubau', 'Pfand', 'Tragepappe zurück', -1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Neubau', 'Sonstiges', 'Regencape', 2.50, 'fa-solid fa-umbrella'],
        ['[Veranstaltungen] - Neubau', 'Snacks', 'Marshmallows 3 Stück', 1.50, 'fa-solid fa-cookie-bite'],
        ['[Veranstaltungen] - Neubau', 'Speisen', 'Waffeln Puderzucker', 2.50, 'fa-solid fa-stroopwafel'],
        ['[Veranstaltungen] - Neubau', 'Speisen', 'Waffeln Belag', 3.00, 'fa-solid fa-stroopwafel'],

        ['[Veranstaltungen] - Chalet', 'Bier / Wein / Sekt', 'Fassbier / Biermix 0,4l', 5.00, 'fa-solid fa-beer-mug-empty'],
        ['[Veranstaltungen] - Chalet', 'Bier / Wein / Sekt', 'Wein  / Schorle 0,2l', 5.00, 'fa-solid fa-wine-glass'],
        ['[Veranstaltungen] - Chalet', 'Bier / Wein / Sekt', 'Wein  / Schorle / Sekt 0,4l', 9.00, 'fa-solid fa-wine-glass'],
        ['[Veranstaltungen] - Chalet', 'AfG', 'Fassbrause  0,4l', 4.50, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Chalet', 'AfG', 'Cola / Limo 0,4l', 4.50, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Chalet', 'AfG', 'Wasser 0,4l', 4.00, 'fa-solid fa-glass-water'],
        ['[Veranstaltungen] - Chalet', 'Speisen', 'Butterbrezel', 3.50, 'fa-solid fa-wheat-awn'],
        ['[Veranstaltungen] - Chalet', 'Heißgetränke', 'Glühwein', 4.00, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Chalet', 'Heißgetränke', 'Kinderpunsch', 3.00, 'fa-solid fa-mug-hot'],
        ['[Veranstaltungen] - Chalet', 'Pfand', 'Pfandbecher', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Chalet', 'Pfand', 'Pfandbecher zurück', -1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Chalet', 'Pfand', 'Tragepappe', 1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Chalet', 'Pfand', 'Tragepappe zurück', -1.00, 'fa-solid fa-money-bill-transfer'],
        ['[Veranstaltungen] - Chalet', 'Sonstiges', 'Regencape', 2.50, 'fa-solid fa-umbrella'],
        ['[Veranstaltungen] - Chalet', 'Speisen', 'Waffeln Puderzucker', 2.50, 'fa-solid fa-cookie-bite'],
        ['[Veranstaltungen] - Chalet', 'Speisen', 'Waffeln Belag', 3.00, 'fa-solid fa-stroopwafel'],
        ['[Veranstaltungen] - Chalet', 'Snacks', 'Marshmallows 3 Stück', 1.50, 'fa-solid fa-stroopwafel'],
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
            [$eventName, $categoryName, $name, $price, $icon] = $data;
            $product = new Product();
            $product->setEvent($events[$eventName]);
            $product->setCategory($categories[$categoryName]);
            $product->setName($name);
            $product->setPrice($price);
            $product->setIcon($icon ?? 'fa-solid fa-circle');

            $manager->persist($product);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [EventFixtures::class, CategoryFixtures::class];
    }
}
