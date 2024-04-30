<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteSettingType extends AbstractType
{

    public  $syndicateId;

    public function __construct($syndicateId)
    {
        $this->syndicateId = $syndicateId;

    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add('modules', 'entity', array(
                'required'      => true,
                'expanded'      =>true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Module',
                'property' => 'name',
                'attr'=>array('class'=>'check-list span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('m')
                            ->where("m.status = 1")
                            ->andWhere("m.slug != 'contact'")
                            ->andWhere("m.slug != 'page'")
                            ->orderBy('m.name','ASC');
                    },
            ))

            ->add('appModules', 'entity', array(
                'required'      => true,
                'expanded'      =>true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\AppModule',
                'property' => 'name',
                'attr'=>array('class'=>'check-list span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('m')
                            ->andWhere("m.status = 1")
                            ->orderBy('m.name','ASC');
                },
            ));


    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\SiteSetting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_sitesetting';
    }
}
