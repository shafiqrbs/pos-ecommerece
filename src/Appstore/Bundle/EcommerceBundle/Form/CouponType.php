<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CouponType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add coupon name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add coupon name'))
                )))
            ->add('couponCode','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add coupon code unique'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add coupon code unique'))
                )))
            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add discount amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add discount amount'))
                )))
            ->add('amountLimit','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add discount amount limit'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add discount amount limit'))
                )))
            ->add('validAmount','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add valid more then amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add valid more then amount'))
                )))
            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add coupon quantity'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add coupon quantity'))
                )))
            ->add('discountCalculation', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    'percentage'       => 'Percentage',
                    'taka'       => 'Taka'
                ),
            ))
            ->add('startDate', 'date', array(
                'widget' => 'choice',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span5'),
                'years' => range(date('Y'), date('Y')+1),
                'view_timezone' => 'Asia/Dhaka'))


            ->add('endDate', 'date', array(
                'widget' => 'choice',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span5'),
                'years' => range(date('Y'), date('Y')+1),
                'view_timezone' => 'Asia/Dhaka'))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Coupon'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coupon';
    }
}
