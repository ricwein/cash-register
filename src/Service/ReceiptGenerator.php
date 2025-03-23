<?php

namespace App\Service;

use App\Enum\PaperSize;
use App\Enum\ReceiptExportType;
use App\Model\ReceiptFilter;
use App\Repository\PurchaseTransactionRepository;
use Dompdf\Dompdf;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class ReceiptGenerator
{
    public function __construct(
        private PurchaseTransactionRepository $purchaseTransactionRepository,
        private TwigEnvironment $twig,
        private ClockInterface $clock,
        #[Autowire('%kernel.project_dir%/public')] private string $publicDir,
    ) {}

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws RuntimeException
     */
    public function generateReceiptPDF(PaperSize $size, ReceiptFilter $filter): Response
    {
        $today = $this->clock->now()->setTime(0, 0);
        $file = $this->buildFilename($size, $filter);

        if (
            $file->isFile()
            && (
                ($filter->getToDate() === null && $filter->getFromDate() < $today)
                || ($filter->getToDate() !== null && $filter->getToDate() < $today)
            )
        ) {
            return $this->buildResponse($file);
        }

        $pdf = match ($filter->getExportType()) {
            ReceiptExportType::PER_EVENT => $this->buildEventPdf($size, $filter, $file),
            ReceiptExportType::ACCUMULATED => $this->buildOverallPdf($size, $filter, $file),
        };

        if (false === file_put_contents($file->getPathname(), $pdf->output())) {
            throw new RuntimeException('Failed to write PDF to file.');
        }

        return $this->buildResponse($file);
    }

    private function buildResponse(SplFileInfo $file): BinaryFileResponse
    {
        return new BinaryFileResponse(
            file: $file,
            headers: [
                'Content-Transfer-Encoding', 'binary',
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$file->getFilename()}\"",
            ]
        );
    }

    private function buildFilename(PaperSize $size, ReceiptFilter $filter): SplFileInfo
    {
        return new SplFileInfo(
            sprintf(
                "%s/Beleg_%s_%s%s_%s.%s.pdf",
                sys_get_temp_dir(),
                strtolower($filter->getExportType()->value),
                $filter->getFromDate()->format('Y-m-d'),
                $filter->getToDate() === null ? '' : ('_' . $filter->getToDate()->format('Y-m-d')),
                empty($filter->getEvents()) ? 'Alle' : implode('+', $filter->getEvents()),
                $size->value,
            )
        );
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function buildEventPdf(PaperSize $size, ReceiptFilter $filter, SplFileInfo $file): Dompdf
    {
        $articles = $this->purchaseTransactionRepository->aggregateArticlesByEvent($filter);
        $dompdf = new Dompdf(['chroot' => $this->publicDir]);
        $content = $this->twig->render('pdf/per_event.html.twig', [
            'size' => $size,
            'articles' => $articles,
            'filter' => $filter,
            'file' => $file,
        ]);
        $dompdf->loadHtml($content, 'UTF-8');
        $dompdf->setPaper($size->size(), $size->orientation());
        $dompdf->render();

        return $dompdf;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function buildOverallPdf(PaperSize $size, ReceiptFilter $filter, SplFileInfo $file): Dompdf
    {
        $articles = $this->purchaseTransactionRepository->aggregateArticlesOverall($filter);

        $dompdf = new Dompdf(['chroot' => $this->publicDir]);
        $content = $this->twig->render('pdf/overall.html.twig', [
            'size' => $size,
            'articles' => $articles,
            'filter' => $filter,
            'file' => $file,
        ]);
        $dompdf->loadHtml($content, 'UTF-8');
        $dompdf->setPaper($size->size(), $size->orientation());
        $dompdf->render();
        return $dompdf;
    }
}
