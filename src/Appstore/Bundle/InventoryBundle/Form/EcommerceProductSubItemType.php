<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Repository\productSizeRepository;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EcommerceProductSubItemType extends AbstractType
{

    /** @var  InventoryConfig */

    private $inventoryConfig;

    /** @var  productSizeRepository */
    private $em;

    function __construct(productSizeRepository $em , InventoryConfig $inventoryConfig)
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

            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Add product quantity' ,'hover'=>'trigger',' data-original-title'=>'Add product quantity')))
            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Add purchase price' ,'hover'=>'trigger',' data-original-title'=>'Add purchase price')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Add sales price','hover'=>'trigger',' data-original-title'=>'Add sales price')))
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
            ->add('colors', 'entity', array(
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
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\GoodsItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'goods_item';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->em->getUseInventoryItemCategory($this->inventoryConfig);

    }
}
