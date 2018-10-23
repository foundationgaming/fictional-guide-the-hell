<?php

namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CustomerFormType extends AbstractType
{   
    public function __construct() {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {                                           
        $builder
            ->add('firstName', TextType::class, array(
                'label' => 'First Name',
                'required' => false
            ))       
            ->add('lastName', TextType::class, array(
                'label' => 'Last Name',
                'required' => false
            ))
            ->add('companyName', TextType::class, array(
                'label' => 'Company Name',
                'required' => false
            ))
            ->add('street', TextType::class, array(
                'required' => false
            ))
            ->add('city', TextType::class, array(
                'required' => false
            ))
            ->add('state', TextType::class, array(
                'required' => false
            ))
            ->add('postcode', TextType::class, array(
                'required' => false
            ))
            ->add('email', TextType::class, array(
                'constraints' => array(
                    new Assert\Email()
                ),
                'label' => 'Email Address',
                'required' => false
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Home / Work Number',
                'required' => false
            ))
            ->add('mobile', TextType::class, array(
                'label' => 'Mobile Number',
                'required' => false
            ))
//            ->add('rowVersion', HiddenType::class)            
            ->add('id', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'quotemaker\domain\Customer',
            'constraints' => [
                new Assert\Callback(function ($data, ExecutionContextInterface $context) {
                    if ($data->getFirstName() == '' && $data->getCompanyName() == '') {
                        $context
                        ->buildViolation('Either First Name or Company Name must be supplied')
                        ->atPath('firstName')
                        ->addViolation();
                    } else if (($data->getFirstName() != '' || $data->getLastName() != '') && $data->getCompanyName() != '') {
                        $context
                        ->buildViolation('Either a Company Name or First Name / Last name must be supplied - not both')
                        ->atPath('companyName')
                        ->addViolation();                        
                    }
                    
                    
                }),
            ],
        ));
    }    
    
}
