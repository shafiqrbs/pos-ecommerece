<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
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
            ->add('paymentMobile','text', array('attr'=>array('required'=> false,'class'=>'m-wrap span12 mobile tooltips','placeholder'=>'Payment mobile account no','data-original-title'=>'Payment mobile account no','autocomplete'=>'off')))
            ->add('transaction','text', array('attr'=>array('required'=> false,'class'=>'m-wrap span12 tooltips','placeholder'=>'Payment transaction id','data-original-title'=>'Payment transaction id','autocomplete'=>'off')))
            ->add('cardNo','text', array('attr'=>array('required'=> false,'class'=>'m-wrap span12 invoice-change','placeholder'=>'Card no','data-original-title'=>'Add payment card no','autocomplete'=>'off')))
            ->add('receive','text', array('attr'=>array('required'=> false,'class'=>'m-wrap span10 numeric tooltips','placeholder'=>'Add payment amount','data-original-title'=>'Add amount','autocomplete'=>'off')))
            ->add('comment','textarea', array('attr'=>array('required'=> false,'class'=>'m-wrap span12 tooltips','placeholder'=>'Enter order related comment','data-original-title'=>'Enter order related comment','autocomplete'=>'off')))
            ->add('timePeriod', 'entity', array(
                'required'    => false,
                'property' => 'name',
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\TimePeriod',
                'attr'=>array('class'=>'span12 m-wrap invoice-change'),
                'empty_value' => '---Select Time Period---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig =".$this->globalOption->getEcommerceConfig()->getId())
                        ->orderBy("e.id","ASC");
                }
            ))
            ->add('paymentCard', 'entity', array(
                'required'    => false,
                'property' => 'name',
                'class' => 'Setting\Bundle\ToolBundle\Entity\PaymentCard',
                'attr'=>array('class'=>'span12 m-wrap invoice-change'),
                'empty_value' => '---Payment card---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))
            ->add('accountMobileBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap '),
                'empty_value' => '---Choose mobile bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))
            ->add('accountBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap '),
                'empty_value' => '---Choose bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))
            ->add('process', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Process Status---',
                'choices' => array(
                    'wfc'       => 'Wait for Confirm',
                    'confirm'   => 'Confirm',
                    'delivered' => 'Delivered',
                )
            ))
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Order'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ecommerce_order';
    }

    protected function LocationChoiceList()
    {
        return  $this->location->getLocationOptionGroup();

    }
}
