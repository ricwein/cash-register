<?php

namespace App\Service\Receipt;

use App\Enum\PaperSize;
use App\Model\ReceiptFilter;
use SplFileInfo;
use Symfony\Component\HttpFoundation\Response;

interface FileGeneratorInterface
{
    public function buildFileResponse(SplFileInfo $file): Response;

    public function generate(SplFileInfo $file, PaperSize $size, ReceiptFilter $filter): Response;
}
