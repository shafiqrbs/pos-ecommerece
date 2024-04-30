<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerForInventoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('facebookId')
            ->add('mobile')
            ->add('district', 'entity', array(
                'required'      => true,
                'multiple'      =>false,
                'expanded'      =>false,
                'class'         => 'SettingLocationBundle:Location',
                'property'      => 'name',
                'attr'          =>array('class'=>'m-wrap span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('d')
                        ->where("d.level = 2")
                        ->orderBy('d.name','ASC');
                }
            ))
            ->add('thana', 'entity', array(
                'required'      =>  true,
                'multiple'      =>  false,
                'expanded'      =>  false,
                'class'         => 'SettingLocationBundle:Location',
                'property'      => 'name',
                'attr'          =>  array('class'=>'m-wrap span12 select2'),
                'query_builder' =>  function(EntityRepository $er){
                    return $er->createQueryBuilder('t')
                        ->where("t.level = 3")
                        ->orderBy('t.name','ASC');
                }
            ))

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_domainuserbundle_customer';
    }
}
