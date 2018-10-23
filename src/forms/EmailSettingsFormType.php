<?php

namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EmailSettingsFormType extends AbstractType
{

    public function __construct() {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder        
            ->add('bodyText', TextareaType::class, array(
                'attr' => array(
                    "rows" => 10
                ),                
                'label' => 'Email Body',
                'required' => false,
            ))
            ->add('footerText', TextareaType::class, array(
                'attr' => array(
                    "rows" => 20
                ),                
                'label' => 'Email Footer',
                'required' => false,
            ));
    }
}
