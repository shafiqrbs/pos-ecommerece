<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PreOrderType extends AbstractType
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
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12 tooltips', 'data-trigger' => 'hover','placeholder'=>'Enter  delivery address ie: Unit,Floor,House,Road,Area,Thana,District Etc','data-original-title'=>'Enter  delivery address ie: Unit,Floor,House,Road,Area,Thana,District Etc','autocomplete'=>'off')))
            ->add('mobileAccount','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Payment mobile account no','data-original-title'=>'Payment mobile account no','autocomplete'=>'off')))
            ->add('transaction','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Payment transaction id','data-original-title'=>'Payment transaction id','autocomplete'=>'off')))
            ->add('paidAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Add payment amount','data-original-title'=>'Add payment amount','autocomplete'=>'off')))

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
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ));

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
