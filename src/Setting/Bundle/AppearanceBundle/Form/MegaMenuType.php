<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Product\Bundle\ProductBundle\Entity\CollectionRepository;
use Setting\Bundle\ToolBundle\Repository\GlobalOptionRepository;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MegaMenuType extends AbstractType
{

    /** @var  CategoryRepository */
    private $em;


    /** @var  SyndicateRepository */
    private $sr;

    /**
     * @var CollectionRepository
     */
    private $cr;

    /**
     * @var GlobalOptionRepository
     */
    private $go;



    function __construct(CategoryRepository $em, CollectionRepository $cr, SyndicateRepository $sr,GlobalOptionRepository $go)
    {
        $this->em = $em;
        $this->sr = $sr;
        $this->cr = $cr;
        $this->go = $go;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter menu group name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('categories', 'entity', array(
                'required'    => true,
                'multiple'      =>true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'category form-control'),
                'class' => 'ProductProductBundle:Category',
                'property' => 'name',
                'choices'=> $this->categoryChoiceList()
            ))

            ->add('syndicates', 'entity', array(
                'required'    => true,
                'multiple'      =>true,
                'empty_value' => '---Select Syndicate---',
                'attr'=>array('class'=>'category form-control'),
                'class' => 'SettingToolBundle:Syndicate',
                'property' => 'name',
                'choices'=> $this->syndcateChoiceList()
            ))

            ->add('brands', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Branding',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('b')
                            ->andWhere("b.status = 1")
                            ->orderBy('b.name','ASC');
                    },
            ))

            ->add('globalOptions', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\GlobalOption',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('b')
                            ->andWhere("b.status = 1")
                            ->orderBy('b.name','ASC');
                    },
            ))


            ->add('collections', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'class' => 'Product\Bundle\ProductBundle\Entity\Collection',
                'property' => 'name',
                'empty_value' => '---Select collection---',
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'choices'=> $this->collectionChoiceList()
            ))


            ->add('status')
        ;
    }



    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\MegaMenu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_appearancebundle_megamenu';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {
        return $categoryTree = $this->em->getCategoryOptionGroup();
    }

    /**
     * @return mixed
     */
    protected function syndcateChoiceList()
    {
        return $syndicateTree = $this->sr->getSyndicateOptionGroup();
    }

    protected function collectionChoiceList()
    {
        return $categoryTree = $this->cr->getCollectionOptionGroup();
    }

    public function globalOptionChoiceList()
    {
        return $categoryTree = $this->go->getGlobalOptionGroup();
    }

}
