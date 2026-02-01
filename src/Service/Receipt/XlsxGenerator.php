<?php

declare(strict_types=1);

namespace App\Service\Receipt;

use App\Enum\ExportFileFormat;
use App\Enum\PaperSize;
use App\Enum\ReceiptExportType;
use App\Helper\ReceiptArticleGroupHelper;
use App\Model\ReceiptArticle;
use App\Model\ReceiptFilter;
use App\Repository\PurchaseTransactionRepository;
use BcMath\Number;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class XlsxGenerator implements FileGeneratorInterface
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
        return ExportFileFormat::EXCEL->value;
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
        $spreadsheet = match ($filter->getExportType()) {
            ReceiptExportType::PER_EVENT => $this->buildEventXlsx($filter),
            ReceiptExportType::ACCUMULATED => $this->buildOverallXlsx($filter),
        };

        $writer = new Xlsx($spreadsheet);
        $writer->save($file->getPathname());

        return $this->buildFileResponse($file);
    }

    private function buildEventXlsx(ReceiptFilter $filter): Spreadsheet
    {
        $articles = $this->purchaseTransactionRepository->aggregateArticlesByEvent($filter);
        [$groupedArticles, $paymentPrices] = $this->groupHelper->groupByEvents($articles);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $currentRow = 1;
        $firstEvent = array_key_first($groupedArticles);

        foreach ($groupedArticles as $eventName => $eventArticles) {
            if ($eventName !== $firstEvent) {
                $currentRow++;
            }

            $dateRange = rtrim($filter->getFromDate()->format('d.m.Y') . ' - ' . $filter->getToDate()?->format('d.m.Y'), '- ');
            $sheet->setCellValue([1, $currentRow], $eventName . ' ' . $dateRange);
            $sheet->mergeCells([1, $currentRow, 3, $currentRow]);
            $sheet->getStyle([1, $currentRow])->getFont()->setBold(true)->setSize(14);
            $currentRow++;

            $currentRow = $this->writeArticlesPerTax($sheet, $eventArticles, $paymentPrices[$eventName], $currentRow);
        }

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    private function buildOverallXlsx(ReceiptFilter $filter): Spreadsheet
    {
        $articles = $this->purchaseTransactionRepository->aggregateArticlesOverall($filter);
        [$groupedArticles, $paymentPrices] = $this->groupHelper->group($articles);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dateRange = rtrim($filter->getFromDate()->format('d.m.Y') . ' - ' . $filter->getToDate()?->format('d.m.Y'), '- ');
        $sheet->setCellValue([1, 1], $dateRange);
        $sheet->mergeCells([1, 1, 3, 1]);
        $sheet->getStyle([1, 1])->getFont()->setBold(true)->setSize(14);

        $this->writeArticlesPerTax($sheet, $groupedArticles, $paymentPrices, 2);

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * @param array<int, array<string, ReceiptArticle>> $articles
     * @param array<int, array<string, Number>> $paymentPrices
     */
    private function writeArticlesPerTax(Worksheet $sheet, array $articles, array $paymentPrices, int $startRow): int
    {
        $currentRow = $startRow;
        $firstTax = array_key_first($articles);

        foreach ($articles as $tax => $articleList) {
            if ($tax !== $firstTax) {
                $currentRow++;
            }

            $sheet->setCellValue([1, $currentRow], $tax . '% ' . $this->translator->trans('SalesTax') . ':');
            $sheet->mergeCells([1, $currentRow, 3, $currentRow]);
            $sheet->getStyle([1, $currentRow])->getFont()->setBold(true);
            $currentRow++;

            $currentRow = $this->writeArticles($sheet, $articleList, $paymentPrices[$tax], $currentRow);
        }

        if (count($articles) > 1) {
            $currentRow++;
            $currentRow = $this->writeArticlesSum($sheet, $paymentPrices, $currentRow);
        }

        return $currentRow;
    }

    /**
     * @param array<int, array<string, Number>> $paymentPrices
     */
    private function writeArticlesSum(Worksheet $sheet, array $paymentPrices, int $currentRow): int
    {
        $sums = [];
        foreach ($paymentPrices as $prices) {
            foreach ($prices as $type => $price) {
                if (array_key_exists($type, $sums)) {
                    $sums[$type] = $sums[$type]->add($price);
                } else {
                    $sums[$type] = $price;
                }
            }
        }

        $sheet->setCellValue([2, $currentRow], $this->translator->trans('Sum'));
        $sheet->mergeCells([2, $currentRow, 3, $currentRow]);
        $sheet->getStyle([2, $currentRow])->getFont()->setBold(true);
        $currentRow++;

        $totalSum = new Number(0);
        foreach ($sums as $type => $sum) {
            if ($sum->compare('0') === 0) {
                continue;
            }
            $totalSum = $totalSum->add($sum);

            $this->writePaymentRow($sheet, $type, $sum, $currentRow);
            $currentRow++;
        }

        $this->writeSummaryRow($sheet, $totalSum, $currentRow);

        return $currentRow + 1;
    }

    /**
     * @param array<string, ReceiptArticle> $articles
     * @param array<string, Number> $paymentPrices
     */
    private function writeArticles(Worksheet $sheet, array $articles, array $paymentPrices, int $currentRow): int
    {
        $sheet->setCellValue([1, $currentRow], $this->translator->trans('Product Name'));
        $sheet->setCellValue([2, $currentRow], $this->translator->trans('Quantity'));
        $sheet->setCellValue([3, $currentRow], $this->translator->trans('Price Sum'));
        $sheet->getStyle([1, $currentRow, 3, $currentRow])->getFont()->setBold(true);
        $currentRow++;

        $priceSum = new Number(0);
        foreach ($articles as $article) {
            $price = $article->price;
            $priceSum = $priceSum->add($price);

            $sheet->setCellValue([1, $currentRow], $article->name);
            $sheet->setCellValue([2, $currentRow], $article->quantity);
            $sheet->setCellValue([3, $currentRow], (float)(string)$price);
            $sheet->getStyle([3, $currentRow])->getNumberFormat()->setFormatCode('#,##0.00');
            $currentRow++;
        }

        $sheet->getStyle([1, $currentRow, 3, $currentRow])->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        foreach ($paymentPrices as $type => $price) {
            $this->writePaymentRow($sheet, $type, $price, $currentRow);
            $currentRow++;
        }

        $this->writeSummaryRow($sheet, $priceSum, $currentRow);

        return $currentRow + 1;
    }

    private function writePaymentRow(Worksheet $sheet, string $type, Number $price, int $currentRow): void
    {
        $sheet->setCellValue([2, $currentRow], ucfirst(strtolower($this->translator->trans($type, domain: 'receipt'))));
        $sheet->getStyle([2, $currentRow])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue([3, $currentRow], (float)(string)$price);
        $sheet->getStyle([3, $currentRow])->getNumberFormat()->setFormatCode('#,##0.00');
    }

    private function writeSummaryRow(Worksheet $sheet, Number $price, int $currentRow): void
    {
        $sheet->setCellValue([2, $currentRow], '∑');
        $sheet->getStyle([2, $currentRow])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue([3, $currentRow], (float)(string)$price);
        $sheet->getStyle([2, $currentRow, 3, $currentRow])->getFont()->setBold(true);
        $sheet->getStyle([3, $currentRow])->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle([3, $currentRow])->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
    }
}
