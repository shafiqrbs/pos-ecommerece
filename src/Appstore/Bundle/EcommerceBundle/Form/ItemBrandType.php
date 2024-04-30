<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemBrandType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add  brand name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  brand name'))
            )))
            ->add('bgcolor','text', array('attr'=>array(
                'class'=>'m-wrap span11 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('featureItem','text', array('attr'=>array(
                'class'=>'m-wrap',
                'placeholder'=>'Feature item limit')
            ))
            ->add('nameBn','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add  brand name bangla'),))
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Brand description')))
            ->add('feature')
            ->add('file', 'file',array(
                'required' => true,
                'constraints' =>array(
                    new File(array(
                        'maxSize' => '1M',
                        'mimeTypes' => array(
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/gif',
                        )
                    ))
                )
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\ItemBrand'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'itemBrand';
    }
}
