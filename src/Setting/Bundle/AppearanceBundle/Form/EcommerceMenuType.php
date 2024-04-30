<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Product\Bundle\ProductBundle\Entity\CollectionRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Repository\GlobalOptionRepository;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EcommerceMenuType extends AbstractType
{

    /** @var  CategoryRepository */
    private $em;

    private $globalOption;


    function __construct(GlobalOption $globalOption ,CategoryRepository $em)
    {
        $this->em = $em;
        $this->globalOption = $globalOption;
        $this->ecommerceConfig = $globalOption->getEcommerceConfig();
        $this->inventoryConfig = $globalOption->getInventoryConfig();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter menu group name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('menu', 'entity', array(
                'required'    => true,
                'multiple'      =>false,
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'empty_value' => '---Select Menu Page---',
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\Menu',
                'property' => 'menu',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                        ->orderBy('e.menu','ASC');
                },
            ))
            ->add('childMenus', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\Menu',
                'property' => 'menu',
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption ={$this->globalOption->getId()}")
                        ->orderBy('e.menu','ASC');
                },
            ))

            ->add('categories', 'entity', array(
                'required'    => true,
                'multiple'      =>true,
                'attr'=>array('class'=>'category form-control'),
                'class' => 'ProductProductBundle:Category',
                'property' => 'name',
                'choices'=> $this->categoryChoiceList()
            ))

            ->add('brands', 'entity', array(
		        'required'    => false,
                'multiple'    => true,
		        'class' => 'Appstore\Bundle\EcommerceBundle\Entity\ItemBrand',
		        'property' => 'name',
		        'attr'=>array('class'=>'m-wrap span12 multiselect'),
		        'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->where("e.status = 1")
			                  ->andWhere("e.ecommerceConfig ={$this->ecommerceConfig->getId()}")
			                  ->orderBy('e.name','ASC');
		        },
	        ))


	        ->add('discounts', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig ={$this->ecommerceConfig->getId()}")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('promotions', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig = {$this->ecommerceConfig->getId()}")
                        ->orderBy('e.name','ASC');
                },
            ))
            ->add('tags', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        /*                        ->andWhere('e.type IN (:type)')
                                              ->setParameter('type', array_values(array('tag')))*/
                                               ->andWhere("e.ecommerceConfig = {$this->ecommerceConfig->getId()}")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('features', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\Feature',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =". $this->globalOption->getId())
                        ->orderBy('e.name','ASC');
                },
            ))
	        ->add('menuPosition', 'choice', array(
		        'attr'=>array('class'=>'span12  m-wrap'),
		        'empty_value' => '---Menu Position ---',
		        'expanded'      =>false,
		        'multiple'      =>false,
		        'choices' => array(
			        'top'       => 'Top',
			        'left'      => 'Left',
			    ),
	        ))
            ->add('singleMenu')

          ;
    }



    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ecommercemenu';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {
       // return $categoryTree = $this->em->getUseEcommerceItemCategory($this->ecommerceConfig);
        return $categoryTree = $this->em->getEcommerceCategoryMenu($this->ecommerceConfig);
    }

}
