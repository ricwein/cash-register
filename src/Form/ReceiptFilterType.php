<?php

namespace App\Form;

use App\Enum\ReceiptExportType;
use App\Model\ReceiptFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('events', ChoiceType::class, [
                'multiple' => true,
                'required' => false,
                'choices' => $options['events'],
            ])
            ->add('exportType', EnumType::class, [
                'class' => ReceiptExportType::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ReceiptFilter::class]);
        $resolver->setRequired(['events']);
        $resolver->addAllowedTypes('events', ['array']);

    }
}
