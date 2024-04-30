<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeatureWidgetType extends AbstractType
{

    /** @var  CategoryRepository */
    private $category;

    private $globalOption;

    public function __construct(GlobalOption $globalOption, CategoryRepository $category)
    {
        $this->globalOption = $globalOption;
        $this->globalId = $globalOption->getId();
        $this->ecommerceConfig = $globalOption->getEcommerceConfig()->getId();
        $this->category = $category;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('menu', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\Menu',
                'empty_value' => '---Select Menu Page---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                ),
                'property' => 'menu',
                'attr'=>array('class'=>'m-wrap span12'),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption = $this->globalId")
                        ->orderBy('e.menu','ASC');
                },
            ))

            ->add('position', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---Select Position---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                ),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'header'                    => 'Header',
                    'body-top'                  => 'Body Top',
                    'body-bottom'               => 'Body Bottom',
                    'footer'                    => 'Footer',
                    'mobile'                    => 'Mobile',
                    'mobile-intro'              => 'Mobile Intro',
                ),
            ))
            ->add('category', 'entity', array(
                'required'    => true,
                'multiple'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'class' => 'ProductProductBundle:Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))

	        ->add('brand', 'entity', array(
		        'required'    => false,
                'multiple'    => true,
		        'class' => 'Appstore\Bundle\EcommerceBundle\Entity\ItemBrand',
		        'empty_value' => '---Select Brand---',
		        'property' => 'name',
		        'attr'=>array('class'=>'m-wrap span12 select2'),
		        'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->where("e.status = 1")
			                  ->andWhere("e.ecommerceConfig =".$this->ecommerceConfig)
			                  ->orderBy('e.name','ASC');
		        },
	        ))

	        ->add('discount', 'entity', array(
                'required'    => false,
                'multiple'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount',
                'empty_value' => '---Select Discount---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig =".$this->ecommerceConfig)
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('promotion', 'entity', array(
                'required'    => false,
                'multiple'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '---Select Promotion---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        /*->andWhere('e.type IN (:type)')
                        ->setParameter('type', array('promotion'))*/
                        ->andWhere("e.ecommerceConfig = $this->ecommerceConfig")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('tag', 'entity', array(
                'required'    => false,
                'multiple'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '---Select Tag---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig = $this->ecommerceConfig")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('jsFeature', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\JsFeature',
                'empty_value' => '---Select Any Feature---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy('e.name','ASC');
                },
            ))
            ->add('content','textarea', array('attr'=>array('class'=>'span12 m-wrap ckeditor','rows' => 6)))
            ->add('sliderFeature', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '---Slider with Feature ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'left-menu'         => 'Left Menu',
                    'category'          => 'Category',
                    'brand'             => 'Brand',
                    'discount'          => 'Discount',
                    'promotion'         => 'Promotion',
                    'tag'               => 'Tag',
                ),
            ))
            ->add('sliderFeaturePosition', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '---Feature Position ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'left'               => 'Left',
                    'right'             => 'Right',
                ),
            ))
            ->add('categoryLimit', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '--- Limit ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    '6'                 => '6',
                    '8'                 => '8',
                    '12'                => '12',
                    '16'                => '16',
                ),
            ))
            ->add('featureCategoryLimit', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '--- Limit ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    '6'                 => '6',
                    '8'                 => '8',
                    '12'                => '12',
                    '16'                => '16',
                ),
            ))
            ->add('brandLimit', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '--- Limit ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    '6'                 => '6',
                    '8'                 => '8',
                    '12'                => '12',
                    '16'                => '16',
                ),
            ))
            ->add('featureBrandLimit', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '--- Limit ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    '6'                 => '6',
                    '8'                 => '8',
                    '12'                => '12',
                    '16'                => '16',
                ),
            ))
            ->add('promotionLimit', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '--- Limit ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    '6'                 => '6',
                    '8'                 => '8',
                    '12'                => '12',
                    '16'                => '16',
                ),
            ))
            ->add('tagLimit', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '--- Limit ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    '6'                 => '6',
                    '8'                 => '8',
                    '12'                => '12',
                    '16'                => '16',
                ),
            ))
            ->add('discountLimit', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '--- Limit ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    '6'                 => '6',
                    '8'                 => '8',
                    '12'                => '12',
                    '16'                => '16',
                ),
            ))
            ->add('categoryShow', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'slider'                    => 'Slider',
                    'imageProduct'              => 'Category image with product',
                    'categorySubCategory'       => 'Category & Sub-category',
                    'grid'                      => 'Product Grid',
                ),
            ))

            ->add('featureCategoryShow', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'slider'              => 'Slider',
                    'grid'                => 'Grid',
                ),
            ))

            ->add('brandShow', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'slider'              => 'Product slider',
                    'imageProduct'        => 'Brand image with product',
                    'grid'                => 'Product grid',
                ),
            ))

            ->add('featureBrandShow', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'slider'              => 'Slider',
                    'grid'                => 'Grid',
                ),
            ))

            ->add('promotionShow', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'slider'              => 'Product slider',
                    'imageProduct'        => 'Promotion image with product',
                    'grid'                => 'Product grid',
                ),
            ))

            ->add('discountShow', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'slider'              => 'Product slider',
                    'imageProduct'        => 'Discount image with product',
                    'grid'                => 'Product grid',
                ),
            ))

            ->add('tagShow', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'slider'              => 'Product slider',
                    'imageProduct'        => 'Tag image with product',
                    'grid'                => 'Product grid',
                ),
            ))
            ->add('featureBrand')
            ->add('featureCategory')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\FeatureWidget'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'featurewidget';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->category->getFlatEcommerceCategoryTree($this->globalOption->getEcommerceConfig());

    }

}
