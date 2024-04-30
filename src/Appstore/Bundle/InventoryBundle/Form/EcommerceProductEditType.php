<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EcommerceProductEditType extends AbstractType
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

            ->add('webName','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Web product name')))
            ->add('masterItem', 'entity', array(
                'required'    => true,
                'empty_value' => '---Choose a master product---',
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Product',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select master item'))
                ),
                'property' => 'nameUnit',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");
                },
            ))

            ->add('brand', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemBrand',
                'property' => 'name',
                'empty_value' => '-Choose a brand-',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");
                },
            ))

            ->add('size', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemSize',
                'empty_value' => '-Choose a size-',
                'property' => 'name',
                'attr'=>array('class'=>'span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->join('p.sizeGroup','sg')
                        ->where("p.status = 1")
                        ->andWhere("p.isValid = 1")
                        ->andWhere("sg.inventoryConfig=".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('country', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\LocationBundle\Entity\Country',
                'empty_value' => '---Choose a country ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy("p.name","ASC");

                },
            ))
            ->add('productUnit', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'empty_value' => '-Choose a unit-',
                'property' => 'name',
                'attr'=>array('class'=>'span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'quantity')))

            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'purchase price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add purchase price'))
            )))

            ->add('overHeadCost','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'over head cost')))

            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'sales price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add sales price'))
            )))
            ->add('content','textarea', array('attr'=>array('class'=>'no-resize span12','rows'=>5)))
            ->add('file')
            /*->add('file','file', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'sales price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please product feature image'))
            )))*/
            ->add('tag', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '-Choose a tags-',
                'property' => 'name',
                'multiple' => 'multiple',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                     $qb = $er->createQueryBuilder('p');
                     $qb->where("p.status = 1");
                     $qb->andWhere($qb->expr()->like('p.type', ':type'));
                     $qb->setParameter('type','%Tag%');
                     $qb->orderBy("p.name","ASC");
                     return $qb;
                },
            ))
            ->add('promotion', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '-Choose a promotion-',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                     $qb = $er->createQueryBuilder('p');
                     $qb->where("p.status = 1");
                     $qb->andWhere($qb->expr()->like('p.type', ':type'));
                     $qb->setParameter('type','%Promotion%');
                     $qb->orderBy("p.name","ASC");
                     return $qb;
                },
            ))
            ->add('discount', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount',
                'empty_value' => '-Choose a Discount-',
                'property' => 'nameDetails',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                     $qb = $er->createQueryBuilder('p');
                     $qb->where("p.status = 1");
                     $qb->orderBy("p.name","ASC");
                     return $qb;
                },
            ))
            ->add('warningText','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('warningLabel', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a warning label---',
                'choices' => array(
                    'Warranty' => 'Warranty',
                    'Guarantee' => 'Guarantee',
                ),
            ))

            ->add('itemColors', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemColor',
                'empty_value' => '-Choose a color-',
                'property' => 'name',
                'multiple' => 'multiple',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.isValid = 1")
                        ->orderBy("p.name","ASC");
                },
            ));
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
