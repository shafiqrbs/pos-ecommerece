<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemAttributeType extends AbstractType
{

	/** @var  EcommerceConfig */

	private $config;


	/** @var  CategoryRepository */
    private $em;

    function __construct(EcommerceConfig $config , CategoryRepository $em)
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Attribute name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  attribute name'))
            )))
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
            ->add('placeholder','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Attribute placeholder')))
            ->add('toolTip','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Attribute tooltip')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\ItemAttribute'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'itemAttribute';
    }

	/**
	 * @return mixed
	 */
	protected function categoryChoiceList()
	{
		return $categoryTree = $this->em->getFlatEcommerceCategoryTree($this->config);
	}

}
