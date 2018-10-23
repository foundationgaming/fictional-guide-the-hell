<?php
namespace quotemaker\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyobFileFormType extends AbstractType
{
    
    private $company_file_choices = null;
    
    public function __construct() {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->company_file_choices = $options['companyFileChoices'];
        $builder->add('companyFile', ChoiceType::class, array(
            'choices' => $this->company_file_choices,
            'choice_label' => 'Name',
            'label' => 'Company File',
            'expanded' => true,
            'multiple' => false
        ))
        ->add('Save', SubmitType::class, array());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'companyFile' => null,
            'companyFileChoices' => null,
        ));
    }
}
