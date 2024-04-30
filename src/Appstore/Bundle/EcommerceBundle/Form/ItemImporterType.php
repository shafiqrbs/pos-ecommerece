<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemImporterType extends AbstractType
{


    /** @var  GlobalOption */
    public  $option;

    public function __construct(GlobalOption $option)
    {
        $this->option = $option;

    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add  file name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please choose  any valid file which extension excel'))
                )))
            ->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountVendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption ={$this->option->getId()}");
                },
            ))
            ->add('file');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\ItemImport'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'importer';
    }
}
