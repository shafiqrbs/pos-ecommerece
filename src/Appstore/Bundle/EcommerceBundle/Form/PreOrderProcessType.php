<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PreOrderProcessType extends AbstractType
{


    /** @var GlobalOption */
    /** @var  LocationRepository */

    public  $globalOption;
    public  $location;

    function __construct(GlobalOption $globalOption , LocationRepository $location)
    {
        $this->globalOption = $globalOption;
        $this->location = $location;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deliveryCharge','text', array('attr'=>array('class'=>'m-wrap span12 numeric tooltips','placeholder'=>'Delivery charge','data-original-title'=>'Delivery charge','autocomplete'=>'off')))
            ->add('discountAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric tooltips','placeholder'=>'Discount amount','data-original-title'=>'Discount amount','autocomplete'=>'off')))
            ->add('prePaidAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric tooltips','placeholder'=>'Pre paid amount','data-original-title'=>'Pre paid amount','autocomplete'=>'off')))
            ->add('deliveryDate','date', array('attr'=>array('class'=>'m-wrap span12 tooltips', 'data-trigger' => 'hover','placeholder'=>'','data-original-title'=>'Please receive your product date(Approximately).',),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'years'=> array('2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'),
                'widget' => 'choice',
                // this is actually the default format for single_text
                'format' => 'dd-MM-yyyy',

            ))
            ->add('comment','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter sms content for pre-order','data-original-title'=>'Enter sms content for pre-order','autocomplete'=>'off')))
            ->add('process', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Select Current Process---',
                'choices' => array(
                    'sms'       => 'Send SMS',
                    'created'       => 'Created',
                    'wfc'       => 'Wait for Confirm',
                    'confirm'       => 'Confirm',
                    'send-delivery'       => 'Send Delivery',
                    'delivered'       => 'Delivered',
                    'returned'       => 'Returned',
                    'cancel'       => 'Cancel',
                    'delete'       => 'Delete',
                ),
            ))
            ->add('cashOnDelivery');
        }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\PreOrder'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_ecommercebundle_preorder';
    }

    protected function LocationChoiceList()
    {
        return  $this->location->getLocationOptionGroup();

    }
}
