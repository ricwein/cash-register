<?php

namespace App\Service\Receipt;

use App\Enum\ExportFileFormat;
use App\Enum\PaperSize;
use App\Enum\ReceiptExportType;
use App\Helper\ReceiptArticleGroupHelper;
use App\Model\ReceiptArticle;
use App\Model\ReceiptFilter;
use App\Repository\PurchaseTransactionRepository;
use BcMath\Number;
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
    ) {
    }

    public function getPrecision(): int
    {
        return 2;
    }

    public static function getFileType(): string
    {
        return ExportFileFormat::CSV->value;
    }

    public function buildFileResponse(SplFileInfo $file): Response
    {
        return new BinaryFileResponse(
            file: $file,
            headers: [
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
            $this->writeArticlesPerTax($csv, $eventArticles, $paymentPrices[$eventName]);
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
        $this->writeArticlesPerTax($csv, $groupedArticles, $paymentPrices);

        return $csv;
    }

    /**
     * @param array<int, array<string, ReceiptArticle>> $articles
     * @param array<int, array<string, Number>> $paymentPrices
     */
    private function writeArticlesPerTax(SplFileObject $csv, array $articles, array $paymentPrices): void
    {
        $firstTax = array_key_first($articles);
        foreach ($articles as $tax => $articleList) {
            if ($tax !== $firstTax) {
                $csv->fputcsv([]);
            }
            $csv->fputcsv([$tax . '% ' . $this->translator->trans('SalesTax').':']);
            $this->writeArticles($csv, $articleList, $paymentPrices[$tax]);
        }

        if (count($articles) > 1) {
            $this->writeArticlesSum($csv, $paymentPrices);
        }
    }

    /**
     * @param array<int, array<string, Number>> $paymentPrices
     */
    private function writeArticlesSum(SplFileObject $csv, array $paymentPrices): void
    {
        $sums = [];
        foreach ($paymentPrices as $prices) {
            foreach ($prices as $type => $price) {
                if (array_key_exists($type, $sums)) {
                    $sums[$type] += $price;
                } else {
                    $sums[$type] = $price;
                }
            }
        }

        $csv->fputcsv([]);
        $csv->fputcsv([
            $this->translator->trans('Sum'),
        ]);

        $totalSum = new Number(0);
        foreach ($sums as $type => $sum) {
            if ($sum->compare('0') === 0) {
                continue;
            }
            $totalSum += $sum;
            $csv->fputcsv([
                ucfirst(strtolower($this->translator->trans($type, domain: 'receipt'))),
                number_format((float)(string)$sum, $this->getPrecision(), ',', '.'),
            ]);
        }

        $csv->fputcsv([
            '∑',
            number_format((float)(string)$totalSum, $this->getPrecision(), ',', '.'),
        ]);
    }

    /**
     * @param array<string, ReceiptArticle> $articles
     * @param array<string, Number> $paymentPrices
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
            $price = $article->price;
            $priceSum += $price;
            $csv->fputcsv([
                $article->name,
                $article->quantity,
                number_format((float)(string)$price, $this->getPrecision(), ',', '.'),
            ]);
        }

        foreach ($paymentPrices as $type => $price) {
            $csv->fputcsv(['', ucfirst(strtolower($this->translator->trans($type, domain: 'receipt'))), number_format((float)(string)$price, $this->getPrecision(), ',', '.')]);
        }

        $csv->fputcsv(['', '∑', number_format((float)(string)$priceSum, $this->getPrecision(), ',', '.')]);
    }
}
