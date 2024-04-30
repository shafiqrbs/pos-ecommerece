<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Setting\Bundle\LocationBundle\Repository\GpLocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class GpCustomerImportType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Import customer excel file name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Import customer excel file'))
                    ))
            )
            ->add('file');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\GpCustomerImport'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'import';
    }


}
