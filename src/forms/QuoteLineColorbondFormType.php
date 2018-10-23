<?php

namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use quotemaker\services\QuotemakerService;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormInterface;

class QuoteLineColorbondFormType extends AbstractType
{

    private $quotemakerService;

    public function __construct(QuotemakerService $service)
    {
        $this->quotemakerService = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('length', TextType::class, array(
                    'label' => "Length (m)",
                    'invalid_message' => 'Please give the length in metres',
                    'error_bubbling' => true,
            ))
            ->add('style', ChoiceType::class, array(
                    'choices'  => $this->quotemakerService->getAllStyles(),
                    'choice_label' => 'description',
                    'choice_value' => 'id',
                    'label' => 'Fence Style',
            ))
            ->add('sheets', ChoiceType::class, array(
                    'choices'  => array(
                            'Smarta' => 'Smarta',
                            'Neeta' => 'Neeta'),
            ))
            ->add('panel', ChoiceType::class, array(
                    'choices'  => $this->quotemakerService->getAllPanels(1),
                    'choice_label' => 'height',
                    'choice_value' => 'id',
                    'label' => 'Height'
            ))
            ->add('colour', ChoiceType::class, array(
                    'choices'  => $this->quotemakerService->getAllColours(),
                    'choice_label' => 'description',
                    'choice_value' => 'id',
                    'label' => 'Colour'
            ))
            ->add('notes', ChoiceType::class, array(
                    'choices'  => array(
                            'Left' => 'L',
                            'Right' => 'R',
                            'Back' => 'B',
                            'Left Front' => 'LF',
                            'Right Front' => 'RF'),
                    'label' => 'Boundary',
            ))
            ->add('quoteId', HiddenType::class)
            ->add('id', HiddenType::class)
            ->add('Save', SubmitType::class, array())
            ->get('length')->addModelTransformer(new CallbackTransformer(
                    function ($lengthInMillis) {
                        // transfrom mm to metres
                        return $lengthInMillis / 1000;
                    },
                    function ($lengthInMetres) {
                        // transform metres to mm
                        $regex = "/^\d+(\.\d+)?$/";
                        if (!preg_match($regex, $lengthInMetres)) {
                            throw new TransformationFailedException('Please give the length in metres');
                        }
                        return $lengthInMetres * 1000;
                    })
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'quotemaker\domain\QuoteLineColorbond',
            'validation_groups' => function (FormInterface $form) {
                return array('Default', 'fence');
            }
        ));
    }
        
}
