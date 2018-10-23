<?php

namespace quotemaker\forms;

use quotemaker\services\QuotemakerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormInterface;

class QuoteLineColorbondGateFormType extends AbstractType
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
            ->add('quoteId', HiddenType::class)
            ->add('id', HiddenType::class)
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

        $builder
            ->add('quantity', IntegerType::class)
            ->add('itemId', HiddenType::class)
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
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ("SG" == $data->getItemType()) {
                    return array('Default', 'single');
                } else if ("DG" == $data->getItemType()) {
                    return array('Default', 'double');
                } else {
                    return array('Default');
                }
            },
        ));
    }
}
