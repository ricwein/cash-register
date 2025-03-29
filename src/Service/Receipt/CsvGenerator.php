<?php

namespace App\Service\Receipt;

use App\Enum\PaperSize;
use App\Enum\ReceiptExportType;
use App\Helper\ReceiptArticleGroupHelper;
use App\Model\ReceiptArticle;
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
        private ReceiptArticleGroupHelper $groupHelper,
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
        $csv->fflush();

        return $this->buildFileResponse($csv);
    }

    private function buildEventCsv(ReceiptFilter $filter, SplFileInfo $file): SplFileObject
    {
        $articles = $this->purchaseTransactionRepository->aggregateArticlesByEvent($filter);

        [$groupedArticles, $paymentPrices] = $this->groupHelper->groupByEvents($articles);

        $csv = new SplFileObject($file->getPathname(), 'wb+');
        $csv->fwrite(chr(0xEF) . chr(0xBB) . chr(0xBF));
        $csv->setCsvControl(';', escape: '');

        $firstEvent = array_key_first($groupedArticles);
        foreach ($groupedArticles as $eventName => $eventArticles) {
            if ($eventName !== $firstEvent) {
                $csv->fputcsv([]);
            }
            $csv->fputcsv([$eventName . ' ' . rtrim($filter->getFromDate()->format('d.m.Y') . ' - ' . $filter->getToDate()?->format('d.m.Y'), '- ')]);
            $this->writeArticles($csv, $eventArticles, $paymentPrices[$eventName]);
        }

        return $csv;
    }

    private function buildOverallCsv(ReceiptFilter $filter, SplFileInfo $file): SplFileObject
    {
        $articles = $this->purchaseTransactionRepository->aggregateArticlesOverall($filter);

        [$groupedArticles, $paymentPrices] = $this->groupHelper->group($articles);

        $csv = new SplFileObject($file->getPathname(), 'wb+');
        $csv->fwrite(chr(0xEF) . chr(0xBB) . chr(0xBF));
        $csv->setCsvControl(';', escape: '');
        $csv->fputcsv([rtrim($filter->getFromDate()->format('d.m.Y') . ' - ' . $filter->getToDate()?->format('d.m.Y'), '- ')]);
        $this->writeArticles($csv, $groupedArticles, $paymentPrices);

        return $csv;
    }

    /**
     * @param ReceiptArticle[] $articles
     * @param array<string, string> $paymentPrices
     */
    private function writeArticles(SplFileObject $csv, array $articles, array $paymentPrices): void
    {
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

        foreach ($paymentPrices as $type => $price) {
            $csv->fputcsv(['', ucfirst(strtolower($this->translator->trans($type))), number_format((float)$price, 2, ',', '.')]);
        }

        $csv->fputcsv(['', '∑', number_format($priceSum, 2, ',', '.')]);
    }
}
