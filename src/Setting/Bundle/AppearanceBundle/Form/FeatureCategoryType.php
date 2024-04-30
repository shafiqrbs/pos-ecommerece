<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Product\Bundle\ProductBundle\Entity\Category;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeatureCategoryType extends AbstractType
{

    private $globalOption;

    /** @var  CategoryRepository */
    private $category;

    public function __construct(GlobalOption $globalOption, CategoryRepository $category)
    {
        $this->globalOption = $globalOption;
        $this->category = $category;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'entity', array(
                'required'    => true,
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Category Name'))
                ),
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'class' => 'ProductProductBundle:Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
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
                        )
                    )),
                    new NotBlank(array('message'=>'Please upload category image'))

                )
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\FeatureCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_appearancebundle_feature_category';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->category->getFlatEcommerceCategoryTree($this->globalOption->getEcommerceConfig());

    }
}
