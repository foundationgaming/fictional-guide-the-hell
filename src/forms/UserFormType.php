<?php
/**
 * Created by PhpStorm.
 * User: AMyers
 * Date: 14/01/2018
 * Time: 3:39 PM
 */

namespace quotemaker\forms;

use quotemaker\domain\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserFormType extends AbstractType
{

    private $encoder; // Used for password encoding
    
    public function __construct($encoder) 
    {
        $this->encoder = $encoder;    
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'constraints' => array(
                    new Assert\NotBlank()
                ),
                'label' => 'Username'
            ))
            ->add('realName', TextType::class, array(
                'constraints' => array(
                    new Assert\NotBlank()
                ),
                'label' => 'Real Name'
            ))
            ->add('emailAddress', TextType::class, array(
                'constraints' => array(
                    new Assert\Optional()
                ),
                'label' => 'Email Address',
                'required' => false
            ))
            ->add('password', PasswordType::class, array())
            ->add('id', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'empty_data' => new User($this->encoder),
            'data_class' => 'quotemaker\domain\User'
        ));
    }

}