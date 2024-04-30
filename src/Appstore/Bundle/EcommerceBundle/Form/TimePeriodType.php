<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimePeriodType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Time period name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter period name'))
            )))
            ->add('nameBn','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Time period name'),))
            ->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\TimePeriod'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'timePeriod';
    }



}
