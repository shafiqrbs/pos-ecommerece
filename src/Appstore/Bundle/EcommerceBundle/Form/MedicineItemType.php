<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedicineItemType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('itemName','text', array('attr'=>array('class'=>'m-wrap span12 select2StockMedicine input','placeholder'=>'Enter stock product name')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span3 input','readonly'=>'readonly','placeholder'=>'MRP')))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span3 form-control input','placeholder'=>'Quantity')));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\OrderItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orderItem';
    }
}
