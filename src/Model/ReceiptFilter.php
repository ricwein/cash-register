<?php

namespace App\Model;

use App\Enum\ExportFileFormat;
use App\Enum\ReceiptExportType;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class ReceiptFilter
{
    public function __construct(
        private DateTimeImmutable $fromDate,
        #[Assert\AtLeastOneOf([
            new Assert\IsNull(message: "Das 'bis Datum' weglassen"),
            new Assert\Expression(
                'value > this.getFromDate()',
                message: "Das 'bis Datum' muss nach dem (start)Datum liegen"
            ),
        ])]
        private ?DateTimeImmutable $toDate = null,
        private ?array $events = null,
        private ReceiptExportType $exportType = ReceiptExportType::ACCUMULATED,
        private ExportFileFormat $fileFormat = ExportFileFormat::CSV,
    ) {}

    public function getFromDate(): DateTimeImmutable
    {
        return $this->fromDate;
    }

    public function setFromDate(DateTimeImmutable $fromDate): self
    {
        $this->fromDate = $fromDate;
        return $this;
    }

    public function getToDate(): ?DateTimeImmutable
    {
        return $this->toDate;
    }

    public function setToDate(?DateTimeImmutable $toDate): self
    {
        $this->toDate = $toDate;
        return $this;
    }

    public function getEvents(): ?array
    {
        return empty($this->events) ? null : $this->events;
    }

    public function setEvents(?array $events): self
    {
        $this->events = $events;
        return $this;
    }

    public function getExportType(): ReceiptExportType
    {
        return $this->exportType;
    }

    public function setExportType(ReceiptExportType $exportType): self
    {
        $this->exportType = $exportType;
        return $this;
    }

    public function getFileFormat(): ExportFileFormat
    {
        return $this->fileFormat;
    }

    public function setFileFormat(ExportFileFormat $fileFormat): self
    {
        $this->fileFormat = $fileFormat;
        return $this;
    }
}
