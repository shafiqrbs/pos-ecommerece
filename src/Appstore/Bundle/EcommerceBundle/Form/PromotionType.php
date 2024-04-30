<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class PromotionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add your promotion/tag name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add your promotion/tag name'))
            )))
            ->add('nameBn','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add your promotion/tag name bangla'),
            ))
            ->add('bgcolor','text', array('attr'=>array(
                'class'=>'m-wrap span11 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('feature')
            ->add('type', 'choice', array(
                'attr'=>array('class'=>'check-list'),
                'expanded'      =>true,
                'multiple'      =>true,
                'choices' => array(
                    'Tag'       => 'Tag',
                    'Promotion'       => 'Promotion'
                ),
            ))
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
                        ),
                        'mimeTypesMessage' => 'Please upload a valid png,jpg,jpeg,gif extension',
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
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'promotion';
    }
}
