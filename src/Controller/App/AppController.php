<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Entity\Event;
use App\Entity\Product;
use App\Entity\PurchasedArticle;
use App\Entity\PurchaseTransaction;
use App\Enum\PaymentType;
use App\Helper\ReceiptArticleGroupHelper;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use App\Service\DTOMapperService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/app')]
class AppController extends AbstractController
{
    private const int PRECISION = ReceiptArticleGroupHelper::PRECISION;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DTOMapperService $dtoMapperService,
        private readonly EventRepository $eventRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductRepository $productRepository,
        private readonly SettingRepository $settingRepository,
    ) {}

    #[Route('/{eventId}', name: 'start_cash_register', requirements: ['eventId' => '\d+'])]
    public function index(int $eventId): Response
    {
        /** @var Event $event */
        $event = $this->eventRepository->find($eventId);
        if ($event === null) {
            throw $this->createNotFoundException();
        }

        $useCategoryTabs = $event->isUseCategoryTabs();
        $categories = $this->dtoMapperService->mapCategories(
            $this->categoryRepository->findAllForEvent($event, loadAdditionalCategories: $useCategoryTabs),
            duplicateCategoryProducts: $useCategoryTabs,
        );

        return $this->render('app/index.html.twig', [
            'event' => $event,
            'settings' => $this->settingRepository->getAllSettings(),
            'categories' => array_filter($categories),
        ]);
    }

    #[Route('/{eventId}/send/{paymentType}', name: 'send_cash_register', requirements: ['eventId' => '\d+'])]
    public function confirmTransaction(int $eventId, PaymentType $paymentType, Request $request): Response
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

        $transaction = new PurchaseTransaction()
            ->setEventName($event->getName())
            ->setPaymentType($paymentType)
            ->setTransactionId(Uuid::v4());

        $price = '0.00';
        foreach ($cartData as $productId => $quantity) {
            $product = $products[$productId];

            $price = bcadd($price, bcmul($product->getPrice(), (string)$quantity, self::PRECISION), self::PRECISION);
            $transaction->addSoldArticle(
                new PurchasedArticle()
                    ->setPrice($product->getPrice())
                    ->setQuantity($quantity)
                    ->setProductName($product->getName())
                    ->setProductId($product->getId())
            );
        }
        $transaction->setPrice($price);

        try {
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return new JsonResponse(['success' => false, 'error' => $exception->getMessage()], 500);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
