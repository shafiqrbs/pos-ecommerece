<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Product\Bundle\ProductBundle\Entity\Category;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeatureBrandType extends AbstractType
{

    private $globalOption;

    public function __construct(GlobalOption $globalOption)
    {
        $this->globalOption = $globalOption;
        $this->ecommerceConfig = $globalOption->getEcommerceConfig();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file','file', array('attr'=>array('class'=>'default'),'constraints' =>array(
                new NotBlank(array('message'=>'Please attach  brand logo'))
            )))
            ->add('brand', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\ItemBrand',
                'empty_value' => '---Select Brand Name---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Brand Name'))
                ),
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig =".$this->ecommerceConfig->getId())
                        ->orderBy('e.name','ASC');
                },
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\FeatureBrand'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'feature_brand';
    }


}
