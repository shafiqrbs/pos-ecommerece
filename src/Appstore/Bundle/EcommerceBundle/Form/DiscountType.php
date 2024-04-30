<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class DiscountType extends AbstractType
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

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Discount title')))
            ->add('nameBn','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Discount title(Bangla) ')))
            ->add('discountAmount','number', array('attr'=>array('class'=>'m-wrap span6 numeric','placeholder'=>'Add discount amount percentage/flat'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add discount amount percentage/flat'))
                )))
            ->add('bgcolor','text', array('attr'=>array(
                'class'=>'m-wrap span11 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('type', 'choice', array(
                'attr'=>array('class'=>'span6'),
                'choices' => array(
                    'percentage'       => 'Percentage',
                    'flat'       => 'Flat'
                ),
            ))

            ->add('featureItem','text', array('attr'=>array(
                'class'=>'m-wrap',
                'placeholder'=>'Feature item limit')
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
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig =".$this->ecommerceConfig)
                        ->orderBy('e.name','ASC');
                },
            ))
            ->add('feature')
            ->add('discountSubItem')
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
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'discount';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {
        return $categoryTree = $this->category->getFlatEcommerceCategoryTree($this->globalOption->getEcommerceConfig());

    }
}
