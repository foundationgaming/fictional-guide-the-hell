<?php

namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class QuoteLineDimensionFormType extends AbstractType
{

    public function __construct() {
        //$this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('length', IntegerType::class)
            ->add('height', IntegerType::class)
            ->add('quantity', IntegerType::class)
            ->add('itemId', HiddenType::class)
            ->add('quoteId', HiddenType::class)
            ->add('Save', SubmitType::class, array(
            ))
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                if ($data->getItemId() == 4) {
                    $form->add('length', IntegerType::class, array(
                        'label' => 'Length between posts (3000mm standard)'));
                }
                if ($data->getItemId() == 3) {
                    $form->add('length', IntegerType::class, array(
                        'label' => 'Length between posts (900mm standard)'));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'quotemaker\domain\QuoteLineDimension',
        ));
    }
}