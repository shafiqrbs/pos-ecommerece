<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EcommerceMedicineType extends AbstractType
{

    /** @var  EcommerceConfig */

    private $config;

    /** @var  CategoryRepository */
    private $em;


    function __construct(CategoryRepository $em , EcommerceConfig $config)
    {
        $this->em = $em;
        $this->config = $config;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('webName','text', array('attr'=>array('class'=>'m-wrap span12 autoComplete2Medicine','placeholder'=>'Product name')))
            ->add('brand', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\ItemBrand',
                'property' => 'name',
                'empty_value' => '-Choose a brand-',
                'attr'=>array('class'=>'span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.ecommerceConfig =".$this->config->getId())
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('warningLabel', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a warning label---',
                'choices' => array(
                    'Warranty' => 'Warranty',
                    'Guarantee' => 'Guarantee',
                ),
            ))

            ->add('itemAssurance', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ItemAssurance',
                'empty_value' => '--Choose a item assurance--',
                'property' => 'name',
                'attr'=>array('class'=>'span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))
	        ->add('category', 'entity', array(
		        'required'    => true,
		        'empty_value' => '---Select product category---',
		        'attr'=>array('class'=>'category m-wrap span12 select2'),
		        'constraints' =>array(
			        new NotBlank(array('message'=>'Please input required'))
		        ),
		        'class' => 'ProductProductBundle:Category',
		        'property' => 'nestedLabel',
		        'choices'=> $this->categoryChoiceList()
	        ))
            ->add('productUnit', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'empty_value' => '-Choose a unit-',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))

            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'quantity')))
            ->add('minQuantity','number', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Min quantity')))
            ->add('maxQuantity','number', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Max quantity')))

            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'purchase price')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'sales price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add sales price'))
            )))
            ->add('shortContent','textarea', array('attr'=>array('class'=>'no-resize span12','rows'=> 5)))
            ->add('subProduct')
            ->add('tag', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '-Choose a tags-',
                'property' => 'name',
                'multiple' => 'multiple',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    $qb = $er->createQueryBuilder('p');
	                $qb->where("p.ecommerceConfig ={$this->config->getId()}");
	                $qb->andWhere("p.status = 1");
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
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'query_builder' => function(EntityRepository $er){
                    $qb = $er->createQueryBuilder('p');
	                $qb->where("p.ecommerceConfig ={$this->config->getId()}");
	                $qb->andWhere("p.status = 1");
                    $qb->andWhere($qb->expr()->like('p.type', ':type'));
                    $qb->setParameter('type','%Promotion%');
                    $qb->orderBy("p.name","ASC");
                    return $qb;
                },
            ))

            ->add('discount', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount',
                'empty_value' => '-Choose a discount-',
                'property' => 'nameDetails',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    $qb = $er->createQueryBuilder('p');
	                $qb->where("p.ecommerceConfig ={$this->config->getId()}");
	                $qb->andWhere("p.status = 1");
                    $qb->orderBy("p.name","ASC");
                    return $qb;
                },
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
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Item'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'item';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {
        return $categoryTree = $this->em->getFlatEcommerceCategoryTree($this->config);
    }
}
