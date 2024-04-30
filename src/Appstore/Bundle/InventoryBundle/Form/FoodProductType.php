<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FoodProductType extends AbstractType
{

    /** @var  InventoryConfig */

    private $inventoryConfig;

    /** @var  CategoryRepository */
    private $em;

    function __construct(CategoryRepository $em , InventoryConfig $inventoryConfig)
    {
        $this->em = $em;
        $this->inventoryConfig = $inventoryConfig;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'product name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add vendor base item name'))
                )))

            ->add('unit', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemUnit',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");
                },
            ))

            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'class' => 'ProductProductBundle:Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'sales price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add sales price'))
            )))
            ->add('webPrice','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'web price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add web price'))
            )))
            ->add('content','textarea', array('attr'=>array('class'=>'no-resize span12','rows'=>8)))
            ->add('file')
            ->add('isWeb')
            ->add('subProduct')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_purchasevendoritem';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->em->getUseInventoryItemCategory($this->inventoryConfig);

    }
}
