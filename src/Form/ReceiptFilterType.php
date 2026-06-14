<?php

namespace App\Form;

use App\Entity\Event;
use App\Enum\ExportFileFormat;
use App\Enum\ReceiptExportType;
use App\Model\ReceiptFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiptFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromDate', DateType::class, ['html5' => true])
            ->add('toDate', DateType::class, ['html5' => true, 'required' => false])
            ->add('events', EntityType::class, [
                'class' => Event::class,
                'multiple' => true,
                'required' => false,
                'translation_domain' => false,
            ])
            ->add('fileFormat', EnumType::class, [
                'class' => ExportFileFormat::class,
            ])
            ->add('exportType', EnumType::class, [
                'class' => ReceiptExportType::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ReceiptFilter::class]);
    }
}
