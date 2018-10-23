<?php

namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\CallbackTransformer;

class QuoteLineNoteFormType extends AbstractType
{

    public function __construct() {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notes', TextareaType::class, array(
                'constraints' => array(
                    new Assert\NotBlank()
                ),
                'label' => 'Note text'
            ))
            ->add('cost', MoneyType::class, array(
                'currency' => 'AUD',
                'invalid_message' => 'Must be an amount in dollars and cents, eg. 100.20'
            ))
            ->add('itemId', HiddenType::class)
            ->add('quoteId', HiddenType::class)
            ->add('Save', SubmitType::class, array());

        $builder
        ->get('cost')->addModelTransformer(new CallbackTransformer(
            function ($priceInCents) {
                // transform the cents into a formatted string (dollars and cents)
                return number_format(($priceInCents / 100), 2, '.', '');
            },
            function ($priceAsString) {
                // transform the string (dollars and cents) back to cents
                return $priceAsString * 100;
            }));
            
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            $itemType = $data->getService()->getItemById($data->getItemId());
            $form->add('notes', TextareaType::class, [
                'attr' => ['data-help'  => $itemType->getInstructions()],
                'label' => 'Note text',
                'constraints' => [new Assert\NotBlank()]
            ]);
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'quotemaker\domain\QuoteLineNote',
        ));
    }
}
