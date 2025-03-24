<?php

namespace App\Service;

use App\Enum\ExportFileFormat;
use App\Enum\PaperSize;
use App\Model\ReceiptFilter;
use App\Service\Receipt\CsvGenerator;
use App\Service\Receipt\PdfGenerator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class ReceiptGenerator
{
    public function __construct(
        private ClockInterface $clock,
        private PdfGenerator $pdfGenerator,
        private CsvGenerator $csvGenerator,
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
            return match ($filter->getFileFormat()) {
                ExportFileFormat::CSV => $this->pdfGenerator->buildFileResponse($file),
                ExportFileFormat::PDF => $this->csvGenerator->buildFileResponse($file),
            };
        }

        return match ($filter->getFileFormat()) {
            ExportFileFormat::PDF => $this->pdfGenerator->generate($file, $size, $filter),
            ExportFileFormat::CSV => $this->csvGenerator->generate($file, $size, $filter),
        };
    }

    private function buildFilename(PaperSize $size, ReceiptFilter $filter): SplFileInfo
    {
        return new SplFileInfo(
            sprintf(
                "%s/Beleg_%s_%s%s_%s.%s.%s",
                sys_get_temp_dir(),
                strtolower($filter->getExportType()->value),
                $filter->getFromDate()->format('Y-m-d'),
                $filter->getToDate() === null ? '' : ('_' . $filter->getToDate()->format('Y-m-d')),
                empty($filter->getEvents()) ? 'Alle' : implode('+', $filter->getEvents()),
                $size->value,
                $filter->getFileFormat()->value,
            )
        );
    }
}
