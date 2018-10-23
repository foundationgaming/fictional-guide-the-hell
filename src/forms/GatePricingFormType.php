<?php

namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GatePricingFormType extends AbstractType
{
    public function __construct(DollarsAndCentsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currencyType = array(
            'currency' => 'AUD',
            'invalid_message' => 'Must be an amount in dollars and cents, eg. 100.20'
        );
        
        $builder
        ->add('rails', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per rail'], 'label' => 'Rails')))
        ->add('rhs35x65', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per lineal metre'], 'label' => "RHS 35x65")))
        ->add('rhs65x65', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per lineal metre'], 'label' => "RHS 65x65")))
        ->add('dLatch', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost of D Latch plus handle and rubber'], 'label' => "D-Latch")))
        ->add('hinges', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per set of hinges'])))
        ->add('dropBolt', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost - 1 needed for double gates only'])))
        ->add('coverStrip', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost - 1 needed per gate'])))
        ->add('postCaps', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost for 1, require 4 for single, 6 for doubles'])))
        ->add('labour', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per gate'])))        

        ->add('powderCoatGate', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Powder Coating - Cost per square metre'], 'label' => "Gate")))
        ->add('powderCoatRHS65x65', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Powder Coating - Cost per lineal metre'], 'label' => "RHS 65x65")))
        ->add('powderCoatDLatch', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Powder Coating - Cost per latch'], 'label' => "D-Latch")))
        ->add('powderCoatHingesCost', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Powder Coating - Cost per 1 hinge'], 'label' => "Hinges")))        
        ->add('powderCoatDropBolt', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Powder Coating - Cost per drop bolt'], 'label' => "Drop bolt")))
        ->add('powderCoatCaps', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Powder Coating - Cost per cap'], 'label' => "Post caps")))        

        ->add('sheetCost1200', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per sheet'], 'label' => "Sheets - 1200mm high")))
        ->add('sheetCost1500', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per sheet'], 'label' => "Sheets - 1500mm high")))
        ->add('sheetCost1800', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per sheet'], 'label' => "Sheets - 1800mm high")))
        ->add('sheetCost2100', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost per sheet'], 'label' => "Sheets - 2100mm high")))
        
        ->add('installSingleCost', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost to install one single gate'], 'label' => "Install - Single Gate")))
        ->add('installDoubleCost', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Cost to install one double gate'], 'label' => "Install - Double Gate")))
        ->add('profitSingleCost', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Profit on a single gate'], 'label' => "Profit - Single Gate")))
        ->add('profitDoubleCost', MoneyType::class, array_merge($currencyType, array('attr' => ['data-help'  => 'Profit on a double gate'], 'label' => "Profit - Double Gate")));
        
        $builder->get('rails')->addModelTransformer($this->transformer);
        $builder->get('rhs35x65')->addModelTransformer($this->transformer);
        $builder->get('rhs65x65')->addModelTransformer($this->transformer);
        $builder->get('dLatch')->addModelTransformer($this->transformer);
        $builder->get('hinges')->addModelTransformer($this->transformer);
        $builder->get('dropBolt')->addModelTransformer($this->transformer);        
        $builder->get('coverStrip')->addModelTransformer($this->transformer);
        $builder->get('postCaps')->addModelTransformer($this->transformer);
        $builder->get('labour')->addModelTransformer($this->transformer);
        
        $builder->get('powderCoatGate')->addModelTransformer($this->transformer);
        $builder->get('powderCoatRHS65x65')->addModelTransformer($this->transformer);
        $builder->get('powderCoatDLatch')->addModelTransformer($this->transformer);
        $builder->get('powderCoatHingesCost')->addModelTransformer($this->transformer);
        $builder->get('powderCoatDropBolt')->addModelTransformer($this->transformer);
        $builder->get('powderCoatCaps')->addModelTransformer($this->transformer);
        
        $builder->get('sheetCost1200')->addModelTransformer($this->transformer);
        $builder->get('sheetCost1500')->addModelTransformer($this->transformer);
        $builder->get('sheetCost1800')->addModelTransformer($this->transformer);
        $builder->get('sheetCost2100')->addModelTransformer($this->transformer);

        $builder->get('installSingleCost')->addModelTransformer($this->transformer);
        $builder->get('installDoubleCost')->addModelTransformer($this->transformer);
        $builder->get('profitSingleCost')->addModelTransformer($this->transformer);
        $builder->get('profitDoubleCost')->addModelTransformer($this->transformer);
        
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'quotemaker\domain\GatePricing',
        ));
    }
    
}