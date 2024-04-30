<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomCategoryType extends AbstractType
{

    /** @var  EcommerceConfig */

    private $config;

    /** @var  CategoryRepository */
    private $em;

    function __construct(EcommerceConfig $config, CategoryRepository $em)
    {
        $this->config = $config;
        $this->em = $em;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter category name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))

            ->add('nameBn','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter category name bangla'),
            ))

            ->add('bgcolor','text', array('attr'=>array(
                'class'=>'m-wrap span11 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('featureItem','text', array('attr'=>array(
                'class'=>'m-wrap',
                'placeholder'=>'Feature item limit')
            ))

            ->add('parent', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'class' => 'ProductProductBundle:Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))
            ->add('feature')
            ->add('homeFeature')
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Category description')))
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
                        )
                    ))
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Product\Bundle\ProductBundle\Entity\Category'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'category';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {
        return $categoryTree = $this->em->getFlatEcommerceCategoryTree($this->config);
    }


}
