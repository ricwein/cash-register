<?php

namespace App\Service;

use App\Enum\PaperSize;
use App\Model\ReceiptFilter;
use App\Service\Receipt\FileGeneratorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use SplFileInfo;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ReceiptGenerator
{
    public function __construct(
        private ClockInterface $clock,
        #[AutowireLocator(FileGeneratorInterface::class, defaultIndexMethod: 'getFileType')] private ContainerInterface $generatorLocator,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function generate(PaperSize $size, ReceiptFilter $filter): Response
    {
        $today = $this->clock->now()->setTime(0, 0);
        $file = $this->buildFilename($size, $filter);

        $format = $filter->getFileFormat()->value;

        /** @var FileGeneratorInterface $generator */
        $generator = $this->generatorLocator->get($format);

        if (
            $file->isFile()
            && (
                ($filter->getToDate() === null && $filter->getFromDate() < $today)
                || ($filter->getToDate() !== null && $filter->getToDate() < $today)
            )
        ) {
            return $generator->buildFileResponse($file);
        }

        return $generator->generate($file, $size, $filter);
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
