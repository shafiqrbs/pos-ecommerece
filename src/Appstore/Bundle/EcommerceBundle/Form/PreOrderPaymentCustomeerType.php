<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PreOrderPaymentCustomeerType extends AbstractType
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
            ->add('mobileAccount','text', array('attr'=>array('class'=>'m-wrap span12 mobile tooltips','placeholder'=>'Payment mobile account no','data-original-title'=>'Payment mobile account no','autocomplete'=>'off')))
            ->add('transaction','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Payment transaction id','data-original-title'=>'Payment transaction id','autocomplete'=>'off')))
            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12 numeric tooltips','placeholder'=>'Add payment amount','data-original-title'=>'Add payment amount','autocomplete'=>'off')))
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
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\PreOrderPayment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ecommerce_payment';
    }

}
