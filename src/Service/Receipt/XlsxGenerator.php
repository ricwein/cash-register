<?php

declare(strict_types=1);

namespace App\Service\Receipt;

use App\Enum\ExportFileFormat;
use App\Enum\PaperSize;
use App\Helper\ReceiptArticleGroupHelper;
use App\Model\ReceiptFilter;
use App\Repository\PurchaseTransactionRepository;
use SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author ricwein_privat
 * @since 01.02.2026
 */
class XlsxGenerator implements FileGeneratorInterface
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
        // TODO: Implement generate() method.
    }
}
