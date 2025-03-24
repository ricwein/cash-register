<?php

namespace App\Service\Receipt;

use App\Enum\PaperSize;
use App\Enum\ReceiptExportType;
use App\Model\ReceiptFilter;
use App\Repository\PurchaseTransactionRepository;
use Dompdf\Dompdf;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class PdfGenerator implements FileGeneratorInterface
{
    public function __construct(
        private PurchaseTransactionRepository $purchaseTransactionRepository,
        private TwigEnvironment $twig,
        #[Autowire('%kernel.project_dir%/public')] private string $publicDir,
    ) {}

    public function buildFileResponse(SplFileInfo $file): Response
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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function generate(SplFileInfo $file, PaperSize $size, ReceiptFilter $filter): Response
    {
        $pdf = match ($filter->getExportType()) {
            ReceiptExportType::PER_EVENT => $this->buildEventPdf($size, $filter, $file),
            ReceiptExportType::ACCUMULATED => $this->buildOverallPdf($size, $filter, $file),
        };

        if (false === file_put_contents($file->getPathname(), $pdf->output())) {
            throw new RuntimeException('Failed to write PDF to file.');
        }

        return $this->buildFileResponse($file);
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
