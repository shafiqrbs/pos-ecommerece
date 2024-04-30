<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CourierServiceType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('companyName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Company name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter company name'))
            )))
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Contact person name'),
                ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Company mobile no'),
                ))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Email address'),
                ))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Courier address'),
                ))
            ->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\CourierService'
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
