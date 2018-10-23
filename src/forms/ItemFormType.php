<?php

namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ItemFormType extends AbstractType
{

    public function __construct() {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, array(
                'constraints' => array(
                    new Assert\NotBlank()
                ),
                'label' => 'Description'
            ))
            ->add('quoteWording', TextType::class, array(
                'constraints' => array(
                    new Assert\NotBlank()
                ),
                'label' => 'Wording on quote'
            ))
            ->add('instructions', TextType::class, array(
                'label' => 'Instructions',
                'required' => false
            ))
            ->add('unitCost', MoneyType::class, array(
                'currency' => 'AUD',
                'invalid_message' => 'Must be an amount in dollars and cents, eg. 100.20'
            ))
            ->add('type', ChoiceType::class, array(
                'choices'  => array(
                    'Single Gate' => 'SG',
                    'Double Gate' => 'DG',
                    'Note' => 'N',
                    'Colourbond' => 'C',
                    'Preformatted Text' => 'PT'
                ),
            ))
            ->add('footerText', TextareaType::class, array(
                'label' => 'Footer Text',
                'attr' => ['data-help'  => "Display on the quote if the quote contains an item of this type"],
                'required' => false
            ))
            ->add('id', HiddenType::class);

        $builder
            ->get('unitCost')->addModelTransformer(new CallbackTransformer(
                function ($priceInCents) {
                    // transform the cents into a formatted string (dollars and cents)
                    return number_format(($priceInCents / 100), 2, '.', '');
                },
                function ($priceAsString) {
                    // transform the string (dollars and cents) back to cents
                    return $priceAsString * 100;
                }));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'quotemaker\domain\Item',
        ));
    }

}
