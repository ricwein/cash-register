<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WebManifestController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly EventRepository $eventRepository,
    ) {}

    #[Route('/.webmanifest', name: 'webmanifest')]
    public function index(): Response
    {
        return new JsonResponse([
            'short_name' => $this->translator->trans('Cash Register'),
            'name' => $this->translator->trans('Cash Register'),
            'description' => $this->translator->trans('Cash Register') . ' | GeyserHaus',
            "icons" => [[
                "src" => "/favicon.svg",
                "type" => "image/svg+xml",
                "sizes" => "256x256"
            ]],
            'id' => $this->generateUrl('redirect_to_admin'),
            'start_url' => "/",
            'background_color' => "#ffffff",
            'display' => "fullscreen",
            'scope' => "/",
            'theme_color' => "#000000",
            'shortcuts' => array_map(fn(Event $event) => [
                'name' => $event->getName(),
                'short_name' => $this->translator->trans('Cash Register') . ' ' . $event->getId(),
                'description' => $this->translator->trans('Cash Register') . ' ' . $event->getName(),
                'url' => $this->generateUrl('start_cash_register', ['eventId' => $event->getId()]),
            ], $this->eventRepository->findAll()),
        ]);
    }

    #[Route('/cash-register', name: 'redirect_to_admin')]
    public function redirectToStart(): Response
    {
        return $this->redirectToRoute('admin');
    }
}
