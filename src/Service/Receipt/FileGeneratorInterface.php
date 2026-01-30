<?php

namespace App\Service\Receipt;

use App\Enum\ExportFileFormat;
use App\Enum\PaperSize;
use App\Model\ReceiptFilter;
use SplFileInfo;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag]
interface FileGeneratorInterface
{
    public function buildFileResponse(SplFileInfo $file): Response;

    public function generate(SplFileInfo $file, PaperSize $size, ReceiptFilter $filter): Response;

    public static function getFileType(): string;
}
