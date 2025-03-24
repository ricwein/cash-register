<?php

namespace App\Service\Receipt;

use App\Enum\PaperSize;
use App\Enum\ReceiptExportType;
use App\Model\ReceiptFilter;
use App\Repository\PurchaseTransactionRepository;
use SplFileInfo;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class CsvGenerator implements FileGeneratorInterface
{
    public function __construct(
        private PurchaseTransactionRepository $purchaseTransactionRepository,
        private TranslatorInterface $translator,
    ) {}

    public function buildFileResponse(SplFileInfo $file): Response
    {
        return new BinaryFileResponse(
            file: $file,
            headers: [
                'Content-Transfer-Encoding', 'binary',
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$file->getFilename()}\"",
            ]
        );
    }

    public function generate(SplFileInfo $file, PaperSize $size, ReceiptFilter $filter): Response
    {
        $csv = match ($filter->getExportType()) {
            ReceiptExportType::PER_EVENT => $this->buildEventCsv($filter, $file),
            ReceiptExportType::ACCUMULATED => $this->buildOverallCsv($filter, $file),
        };

        return $this->buildFileResponse($csv);
    }

    private function buildEventCsv(ReceiptFilter $filter, SplFileInfo $file): SplFileObject
    {
        $articles = $this->purchaseTransactionRepository->aggregateArticlesByEvent($filter);

        $csv = new SplFileObject($file->getPathname(), 'wb+');
        $csv->fwrite(chr(0xEF) . chr(0xBB) . chr(0xBF));
        $csv->setCsvControl(';', escape: '');

        $firstEvent = array_key_first($articles);
        foreach ($articles as $eventName => $eventArticles) {
            if ($eventName !== $firstEvent) {
                $csv->fputcsv([]);
            }
            $csv->fputcsv([$eventName]);
            $csv->fputcsv([
                $this->translator->trans('Product Name'),
                $this->translator->trans('Quantity'),
                $this->translator->trans('Price Sum'),
            ]);

            $priceSum = 0.00;
            foreach ($eventArticles as $article) {
                $price = (float)$article->price;
                $priceSum += $price;
                $csv->fputcsv([
                    $article->name,
                    $article->quantity,
                    number_format($price, 2, ',', '.'),
                ]);
            }
            $csv->fputcsv(['', '', number_format($priceSum, 2, ',', '.')]);
        }

        return $csv;
    }

    private function buildOverallCsv(ReceiptFilter $filter, SplFileInfo $file): SplFileObject
    {
        $articles = $this->purchaseTransactionRepository->aggregateArticlesOverall($filter);

        $csv = new SplFileObject($file->getPathname(), 'wb+');
        $csv->fwrite(chr(0xEF) . chr(0xBB) . chr(0xBF));
        $csv->setCsvControl(';', escape: '');
        $csv->fputcsv([
            $this->translator->trans('Product Name'),
            $this->translator->trans('Quantity'),
            $this->translator->trans('Price Sum'),
        ]);

        $priceSum = 0.00;
        foreach ($articles as $article) {
            $price = (float)$article->price;
            $priceSum += $price;
            $csv->fputcsv([
                $article->name,
                $article->quantity,
                number_format($price, 2, ',', '.'),
            ]);
        }
        $csv->fputcsv(['', '', number_format($priceSum, 2, ',', '.')]);

        return $csv;
    }
}
