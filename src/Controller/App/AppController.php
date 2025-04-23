<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Entity\Event;
use App\Entity\Product;
use App\Entity\PurchasedArticle;
use App\Entity\PurchaseTransaction;
use App\Enum\ConfirmationState;
use App\Helper\ReceiptArticleGroupHelper;
use App\Model\PaymentTransaction;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use App\Resolver\PaymentTransactionRequestResolver;
use App\Service\DTOMapperService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        private readonly TranslatorInterface $translator,
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

    #[Route('/{eventId}/send', name: 'send_cash_register', requirements: ['eventId' => '\d+'], methods: ['PUT'])]
    public function confirmTransaction(
        int $eventId,
        #[MapRequestPayload(resolver: PaymentTransactionRequestResolver::class)] PaymentTransaction $transactionData,
    ): Response {
        $event = $this->eventRepository->find($eventId);
        if ($event === null) {
            return new JsonResponse([
                'state' => ConfirmationState::ERROR,
                'message' => "Event with id {$eventId} not found",
            ], 404);
        }

        $products = [];
        /** @var Product $product */
        foreach ($this->productRepository->findBy(['id' => array_keys($transactionData->cart)]) as $product) {
            $products[$product->getId()] = $product;
        }

        $transaction = new PurchaseTransaction()
            ->setEventName($event->getName())
            ->setPaymentType($transactionData->paymentType)
            ->setTransactionId($transactionData->getUuid());

        $price = '0.00';
        foreach ($transactionData->cart as $productId => $quantity) {
            if (null === $product = ($products[$productId] ?? null)) {
                return new JsonResponse([
                    'state' => ConfirmationState::ERROR,
                    'message' => "Product with id {$productId} not found",
                ], 404);
            }

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
            return new JsonResponse([
                'state' => ConfirmationState::ERROR,
                'message' => $exception->getMessage(),
            ], 500);
        }

        return new JsonResponse([
            'state' => ConfirmationState::SUCCESS,
            'message' => $this->translator->trans('processed'),
        ]);
    }
}
