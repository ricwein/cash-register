<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\SalesTaxRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    private const array PRODUCTS = [
        [['Tagesbetrieb'], 'Heißgetränke', 'Filterkaffee klein', 2.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Filterkaffee groß', 3.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Kaffee crème klein', 3.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Kaffee crème groß', 4.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Cappuccino klein', 3.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Cappuccino groß', 4.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Milchkaffee klein', 3.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Milchkaffee groß', 4.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Latte macchiato', 4.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Espresso klein', 2.50, 'mdi mdi-coffee-maker', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Espresso groß', 3.50, 'mdi mdi-coffee-maker', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Schokokaffee klein', 3.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Schokokaffee groß', 4.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Heiße Schokolade klein', 3.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Heiße Schokolade groß', 4.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Tee', 3.00, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Glühwein', 3.50, 'fa-solid fa-mug-hot', 2],
        [['Tagesbetrieb'], 'Heißgetränke', 'Kinderpunsch', 2.50, 'fa-solid fa-mug-hot', 2],

        [['Tagesbetrieb'], 'AfG', 'Wasser klein', 2.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Wasser groß', 3.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Fassbrause klein', 2.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Fassbrause groß', 3.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Cola / Limo klein', 2.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Cola / Limo groß', 3.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Saft / Saftschorle klein', 3.00, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Saft / Saftschorle groß', 4.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Club Mate', 3.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Fritz Limo / Cola 0,33l', 3.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Bio Elsterschorle 0,33l', 3.50, 'fa-solid fa-glass-water', 2],
        [['Tagesbetrieb'], 'AfG', 'Rauch Saft 0,33l', 3.50, 'fa-solid fa-glass-water', 2],

        [['Tagesbetrieb'], 'Eisgetränke', 'Eisschokolade', 5.00, 'fa-solid fa-whiskey-glass', 2],
        [['Tagesbetrieb'], 'Eisgetränke', 'Eiskaffee', 5.00, 'fa-solid fa-whiskey-glass', 2],
        [['Tagesbetrieb'], 'Eisgetränke', 'Kugel Eis', 1.50, 'fa-solid fa-whiskey-glass', 2],

        [['Tagesbetrieb'], 'Bier / Wein / Sekt', 'Fassbier / Biermix 0,4l', 4.50, 'fa-solid fa-beer-mug-empty', 2],
        [['Tagesbetrieb'], 'Bier / Wein / Sekt', 'Flaschenbier 0,5l', 4.50, 'fa-solid fa-beer-mug-empty', 2],
        [['Tagesbetrieb'], 'Bier / Wein / Sekt', 'Wein / Weinschorle 0,2l', 4.50, 'fa-solid fa-wine-glass', 2],
        [['Tagesbetrieb'], 'Bier / Wein / Sekt', 'Wein / Weinschorle 0,4l', 8.00, 'fa-solid fa-wine-glass', 2],
        [['Tagesbetrieb'], 'Bier / Wein / Sekt', 'Sekt 0,2l', 4.50, 'fa-solid fa-champagne-glasses', 2],
        [['Tagesbetrieb'], 'Bier / Wein / Sekt', 'Sekt 0,4l', 8.00, 'fa-solid fa-champagne-glasses', 2],

        [['Tagesbetrieb'], 'Pfand', 'Pfand Ausgabe', 1.00, 'fa-solid fa-money-bill-transfer', 1],
        [['Tagesbetrieb'], 'Pfand', 'Pfandbecher zurück', -1.00, 'fa-solid fa-money-bill-transfer', 1],
        [['Tagesbetrieb'], 'Pfand', 'Tragepappe', 1.00, 'fa-solid fa-money-bill-transfer', 1],
        [['Tagesbetrieb'], 'Pfand', 'Tragepappe zurück', -1.00, 'fa-solid fa-money-bill-transfer', 1],

        [['Tagesbetrieb'], 'Eis', 'Eis', 1.00, 'fa-solid fa-ice-cream', 1],
        [['Tagesbetrieb'], 'Eis', 'Eis', 1.50, 'fa-solid fa-ice-cream', 1],
        [['Tagesbetrieb'], 'Eis', 'Eis', 1.50, 'fa-solid fa-ice-cream', 1],
        [['Tagesbetrieb'], 'Eis', 'Eis', 2.00, 'fa-solid fa-ice-cream', 1],
        [['Tagesbetrieb'], 'Eis', 'Eis', 2.00, 'fa-solid fa-ice-cream', 1],
        [['Tagesbetrieb'], 'Eis', 'Eis', 2.00, 'fa-solid fa-ice-cream', 1],
        [['Tagesbetrieb'], 'Eis', 'Eis', 3.50, 'fa-solid fa-ice-cream', 1],
        [['Tagesbetrieb'], 'Eis', 'Eis', 4.00, 'fa-solid fa-ice-cream', 1],
        [['Tagesbetrieb'], 'Eis', 'Eis', 4.50, 'fa-solid fa-ice-cream', 1],

        [['Tagesbetrieb'], 'Speisen', 'Butterbrezel', 3.00, 'mdi mdi-pretzel', 1],
        [['Tagesbetrieb'], 'Speisen', 'Bockwurst', 3.50, 'fa-solid fa-hotdog', 1],
        [['Tagesbetrieb'], 'Speisen', 'Wiener', 4.00, 'fa-solid fa-hotdog', 1],
        [['Tagesbetrieb'], 'Speisen', 'Currywurst', 4.50, 'fa-solid fa-hotdog', 1],
        [['Tagesbetrieb'], 'Speisen', 'Brötchen', 0.50, 'mdi mdi-baguette', 1],

        [['Tagesbetrieb'], 'Snacks', 'Chips', 2.50, 'fa-solid fa-cookie-bite', 1],
        [['Tagesbetrieb'], 'Snacks', 'Nüsse', 2.00, 'mdi mdi-peanut', 1],
        [['Tagesbetrieb'], 'Snacks', 'Lollies', 0.50, 'fa-solid fa-cookie-bite', 1],
        [['Tagesbetrieb'], 'Snacks', 'Pick up ', 1.00, 'fa-solid fa-cookie-bite', 1],
        [['Tagesbetrieb'], 'Snacks', 'Ballisto', 1.50, 'fa-solid fa-cookie-bite', 1],
        [['Tagesbetrieb'], 'Snacks', 'Gummibärchen', 0.50, 'fa-solid fa-cookie-bite', 1],
        [['Tagesbetrieb'], 'Snacks', 'Marshmallows 3 Stück', 1.50, 'fa-solid fa-cookie-bite', 1],

        [['Tagesbetrieb'], 'Kuchen', 'Kuchen', 2.50, 'fa-solid fa-cake-candles', 1],
        [['Tagesbetrieb'], 'Kuchen', 'Kuchen', 3.00, 'fa-solid fa-cake-candles', 1],
        [['Tagesbetrieb'], 'Kuchen', 'Kuchen', 3.50, 'fa-solid fa-cake-candles', 1],
        [['Tagesbetrieb'], 'Kuchen', 'Kuchen', 4.00, 'fa-solid fa-cake-candles', 1],

        [['Tagesbetrieb'], 'Grill', 'Bratwurst', 3.50, 'fa-solid fa-hotdog', 1],
        [['Tagesbetrieb'], 'Grill', 'Steak', 4.50, 'mdi mdi-food-steak', 1],
        [['Tagesbetrieb'], 'Grill', 'Gemüsefrikadelle', 3.00, 'fa-solid fa-utensils', 1],
        [['Tagesbetrieb'], 'Grill', 'Blumenkohl-Käse-Medaillon', 3.50, 'fa-solid fa-utensils', 1],
        [['Tagesbetrieb'], 'Grill', 'Gemüseschnitzel', 3.50, 'fa-solid fa-utensils', 1],
        [['Tagesbetrieb'], 'Grill', 'Bratwurst ohne Brötchen', 3.00, 'fa-solid fa-hotdog', 1],
        [['Tagesbetrieb'], 'Grill', 'Steak ohne Brötchen', 4.00, 'mdi mdi-food-steak', 1],
        [['Tagesbetrieb'], 'Grill', 'Gemüsefrikadelle ohne Brötchen', 2.50, 'fa-solid fa-utensils', 1],
        [['Tagesbetrieb'], 'Grill', 'Blumenkohl-Käse-Medaillon ohne Brötchen', 3.00, 'fa-solid fa-utensils', 1],
        [['Tagesbetrieb'], 'Grill', 'Gemüseschnitzel ohne Brötchen', 3.00, 'fa-solid fa-utensils', 1],

        [['Tagesbetrieb'], 'Tabak etc.', 'Filter', 1.50, 'fa-solid fa-smoking', 1],
        [['Tagesbetrieb'], 'Tabak etc.', 'GIZEH Papers', 1.00, 'fa-solid fa-smoking', 1],
        [['Tagesbetrieb'], 'Tabak etc.', 'GIZEH Paper doppelt', 1.50, 'fa-solid fa-smoking', 1],
        [['Tagesbetrieb'], 'Tabak etc.', 'Pueblo', 6.50, 'fa-solid fa-smoking', 1],

        [['[Veranstaltungen] - Grill', '[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Speisen', 'Butterbrezel ', 3.50, 'mdi mdi-pretzel', 1],
        [['[Veranstaltungen] - Grill'], 'Speisen', 'Brötchen', 0.50, 'mdi mdi-baguette', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Bratwurst', 4.00, 'fa-solid fa-hotdog', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Steak ', 5.00, 'mdi mdi-food-steak', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Gemüsefrikadelle ', 3.50, 'fa-solid fa-utensils', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Blumenkohl-Käse-Medaillon', 4.00, 'fa-solid fa-utensils', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Gemüseschnitzel', 4.50, 'fa-solid fa-utensils', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Bratwurst ohne Brötchen ', 3.50, 'fa-solid fa-hotdog', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Steak ohne Brötchen ', 4.50, 'mdi mdi-food-steak', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Gemüsefrikadelle ohne Brötchen ', 3.00, 'fa-solid fa-utensils', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Blumenkohl-Käse-Medaillon ohne Brötchen ', 3.50, 'fa-solid fa-utensils', 1],
        [['[Veranstaltungen] - Grill'], 'Grill', 'Gemüseschnitzel ohne Brötchen ', 4.00, 'fa-solid fa-utensils', 1],
        [['[Veranstaltungen] - Grill', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Snacks', 'Marshmallows 3 Stück', 1.50, 'fa-solid fa-utensils', 1],

        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Bier / Wein / Sekt', 'Fassbier / Biermix 0,4l', 5.00, 'fa-solid fa-beer-mug-empty', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Bier / Wein / Sekt', 'Wein / Schorle / Sekt 0,2l', 5.00, 'fa-solid fa-wine-glass', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Bier / Wein / Sekt', 'Wein / Schorle / Sekt 0,4l', 9.00, 'fa-solid fa-wine-glass', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'AfG', 'Fassbrause 0,4l', 4.50, 'fa-solid fa-glass-water', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'AfG', 'Cola / Limo 0,4l', 4.50, 'fa-solid fa-glass-water', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau'], 'AfG', 'Saft / Schorle 0,4l', 5.00, 'fa-solid fa-glass-water', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'AfG', 'Wasser 0,4l', 4.00, 'fa-solid fa-glass-water', 2],

        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau'], 'Heißgetränke', 'Filterkaffee', 2.50, 'fa-solid fa-mug-hot', 2],
        [['[Veranstaltungen] - Kiosk'], 'Heißgetränke', 'Tee', 3.00, 'fa-solid fa-mug-hot', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Heißgetränke', 'Glühwein', 4.00, 'fa-solid fa-mug-hot', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Heißgetränke', 'Kinderpunsch', 3.00, 'fa-solid fa-mug-hot', 2],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Pfand', 'Pfandbecher', 1.00, 'fa-solid fa-money-bill-transfer', 1],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Pfand', 'Pfandbecher zurück', -1.00, 'fa-solid fa-money-bill-transfer', 1],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Pfand', 'Tragepappe', 1.00, 'fa-solid fa-money-bill-transfer', 1],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Pfand', 'Tragepappe zurück', -1.00, 'fa-solid fa-money-bill-transfer', 1],
        [['[Veranstaltungen] - Kiosk', '[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Sonstiges', 'Regencape', 2.50, 'fa-solid fa-umbrella', 1],
        [['[Veranstaltungen] - Kiosk'], 'Tabak etc.', 'Filter', 1.50, 'fa-solid fa-smoking', 1],
        [['[Veranstaltungen] - Kiosk'], 'Tabak etc.', 'GIZEH Papers', 1.00, 'fa-solid fa-smoking', 1],
        [['[Veranstaltungen] - Kiosk'], 'Tabak etc.', 'GIZEH Paper doppelt', 1.50, 'fa-solid fa-smoking', 1],
        [['[Veranstaltungen] - Kiosk'], 'Tabak etc.', 'Pueblo', 6.50, 'fa-solid fa-smoking', 1],

        [['[Veranstaltungen] - Neubau'], 'Snacks', 'Chips', 2.50, 'fa-solid fa-cookie-bite', 1],
        [['[Veranstaltungen] - Neubau'], 'Snacks', 'Nüsse', 2.00, 'mdi mdi-peanut', 1],
        [['[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Speisen', 'Waffeln Puderzucker', 2.50, 'fa-solid fa-stroopwafel', 1],
        [['[Veranstaltungen] - Neubau', '[Veranstaltungen] - Chalet'], 'Speisen', 'Waffeln Belag', 3.00, 'fa-solid fa-stroopwafel', 1],
    ];

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly SalesTaxRepository $salesTaxRepository,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $events = [];
        foreach ($this->eventRepository->findAll() as $event) {
            $events[$event->getName()] = $event;
        }

        $taxes = [];
        foreach ($this->salesTaxRepository->findAll() as $tax) {
            $taxes[$tax->getId()] = $tax;
        }

        $categories = [];
        foreach ($this->categoryRepository->findAll() as $category) {
            $categories[$category->getName()] = $category;
        }

        foreach (self::PRODUCTS as $data) {
            [$eventNames, $categoryName, $name, $price, $icon, $taxId] = $data;
            $product = new Product();
            foreach ($eventNames as $eventName) {
                $product->addEvent($events[$eventName]);
            }

            $product->setCategory($categories[$categoryName]);
            $product->setName($name);
            $product->setPrice($price);
            $product->setTax($taxes[$taxId]);
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
