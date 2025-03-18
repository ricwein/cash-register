<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Product;
use App\Entity\Sale;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use App\Service\DTOMapperService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app')]
class AppController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DTOMapperService $dtoMapperService,
        private readonly EventRepository $eventRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductRepository $productRepository,
    ) {}

    #[Route('/{eventId}', name: 'start_cash_register', requirements: ['eventId' => '\d+'])]
    public function index(int $eventId): Response
    {
        /** @var Event $event */
        $event = $this->eventRepository->find($eventId);
        if ($event === null) {
            throw $this->createNotFoundException();
        }

        $categories = array_map(
            fn(Category $category) => $this->dtoMapperService->mapCategory($category),
            $this->categoryRepository->findAllForEvent($event),
        );

        return $this->render('app/index.html.twig', [
            'event' => $event,
            'categories' => array_filter($categories),
        ]);
    }

    #[Route('/{eventId}/send', name: 'send_cash_register', requirements: ['eventId' => '\d+'])]
    public function confirmTransaction(int $eventId, Request $request): Response
    {
        $event = $this->eventRepository->find($eventId);
        if ($event === null) {
            return new JsonResponse(['success' => false, 'error' => "Event with id {$eventId} not found"], 404);
        }

        try {
            /** @noinspection JsonEncodingApiUsageInspection */
            $cartData = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return new JsonResponse(['success' => false, 'error' => $exception->getMessage()], 500);
        }

        $products = [];
        /** @var Product $product */
        foreach ($this->productRepository->findBy(['id' => array_keys($cartData)]) as $product) {
            $products[$product->getId()] = $product;
        }

        foreach ($cartData as $productId => $quantity) {
            $product = $products[$productId];
            $sale = new Sale();
            $sale->setEventName($event->getName());
            $sale->setQuantity($quantity);
            $sale->setPricePerItem($product->getPrice());
            $sale->setPrice(bcmul($product->getPrice(), (string)$quantity, 2));
            $sale->setProductName($product->getName());

            $this->entityManager->persist($sale);
        }
        try {
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return new JsonResponse(['success' => false, 'error' => $exception->getMessage()], 500);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
