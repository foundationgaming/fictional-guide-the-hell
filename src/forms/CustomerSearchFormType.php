<?php

namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomerSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerName', TextType::class,
                array('constraints' => array (),
                    'label' => 'Customer Name',
                    'required' => false
                ))
            ->add('quoteId', TextType::class,
                array('constraints' => array (),
                    'label' => 'Quote ID',
                    'required' => false
                ))
            ->add('customerSearchButton', SubmitType::class, array('label' => 'Search'))
            ->add('quoteIDSearchButton', SubmitType::class, array('label' => 'Search'));
    }

    public function getName()
    {
        return 'app_customer_search';
    }
}
