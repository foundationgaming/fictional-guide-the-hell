<?php

namespace quotemaker\forms;

use quotemaker\services\QuotemakerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class PanelFormType extends AbstractType
{
    private $quotemakerService;

    public function __construct(QuotemakerService $service)
    {
        $this->quotemakerService = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fenceType', ChoiceType::class, array(
                'choices' => $this->quotemakerService->getAllFenceTypeAsChoiceArray()
            ))
            ->add('width', IntegerType::class)
            ->add('height', IntegerType::class)
            ->add('postLength', IntegerType::class)
            ->add('price', MoneyType::class, array(
                'currency' => 'AUD',
                'invalid_message' => 'Must be an amount in dollars and cents, eg. 100.20'
            ))
            ->add('installation', MoneyType::class, array(
                'currency' => 'AUD',
                'invalid_message' => 'Must be an amount in dollars and cents, eg. 100.20'
            ))
            ->add('id', HiddenType::class);

        $builder
            ->get('price')->addModelTransformer(new CallbackTransformer(
            function ($priceInCents) {
                // transform the cents into a formatted string (dollars and cents)
                return number_format(($priceInCents / 100), 2, '.', '');
            },
            function ($priceAsString) {
                // transform the string (dollars and cents) back to cents
                return $priceAsString * 100;
            }));
        $builder
            ->get('installation')->addModelTransformer(new CallbackTransformer(
                function ($priceInCents) {
                    // transform the cents into a formatted string (dollars and cents)
                    return number_format(($priceInCents / 100), 2, '.', '');
                },
                function ($priceAsString) {
                    // transform the string (dollars and cents) back to cents
                    return $priceAsString * 100;
                }
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'quotemaker\domain\Panel',
        ));
    }

}
