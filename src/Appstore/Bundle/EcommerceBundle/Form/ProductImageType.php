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

class ProductImageType extends AbstractType
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
            ->add('file', 'file',array(
                'required' => true,
                'constraints' =>array(
                    new File(array(
                        'maxSize' => '5M',
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
