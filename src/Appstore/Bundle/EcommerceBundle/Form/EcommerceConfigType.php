<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EcommerceConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('shippingCharge','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Please set delivery charge')))
            ->add('pickupLocation','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 6,'placeholder'=>'Please set address where customer product pickup')))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 6 ,'placeholder'=>'Please set address ')))
            ->add('currency', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '৳'       => 'Taka(৳)',
                    '$'       => 'Dollar($)'
                ),
            ))
            ->add('stockApplication', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\AppModule',
                'empty_value' => '---Select Main Application ---',
                'property' => 'name',
                'attr'     =>array('id' => '' , 'class' => 'm-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->andWhere("s.status = 1")
                        ->orderBy('s.name','ASC');
                },
            ))
            ->add('showBengal', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'empty_value' => '-Choose Product Name-',
                'choices' => array(
                    'englishbangla' => 'English(Bangla)',
                    'english-bangla' => 'English-Bangla',
                    'bangla'       => 'Bangla'
                ),
            ))
            ->add('cartProcess', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'empty_value' => '-Choose Cart Process-',
                'choices' => array(
                    'single' => 'Single',
                    'inline' => 'Inline',
                ),
            ))
            ->add('perPage', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '15'       => 'Per page-15',
                    '16'       => 'Per page-16',
                    '20'       => 'Per page-20',
                    '21'       => 'Per page-21',
                    '24'       => 'Per page-24',
                    '27'       => 'Per page-27',
                    '28'       => 'Per page-28',
                    '30'       => 'Per page-30',
                ),
            ))
            ->add('perColumn', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '6'       => 'Per Column-6',
                    '5'       => 'Per Column-5',
                    '4'       => 'Per Column-4',
                    '3'       => 'Per Column-3',
                    '2'       => 'Per Column-2',
                ),
            ))
            ->add('menuType', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'Mega'       => 'Mega',
                    'Dropdown'       => 'Dropdown',
                    'Sidebar'       => 'Sidebar',
                ),
            ))
            ->add('owlProductColumn', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '3'       => 'Per Column-3',
                    '4'       => 'Per Column-4',
                    '5'       => 'Per Column-5',
                    '6'       => 'Per Column-6',
                ),
            ))
             ->add('gridProductColumn', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '2'       => 'Per Column-2',
                    '3'       => 'Per Column-3',
                    '4'       => 'Per Column-4',
                    '5'       => 'Per Column-5',
                    '6'       => 'Per Column-6',
                ),
            ))
             ->add('mobileProductColumn', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '1'       => 'Per Column-1',
                    '2'       => 'Per Column-2',
                    '3'       => 'Per Column-3',
                ),
            ))
            ->add('mobileFeatureColumn', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '1'       => 'Per Column-1',
                    '2'       => 'Per Column-2',
                    '3'       => 'Per Column-3',
                ),
            ))
            ->add('paginationShow', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'top'       => 'Top',
                    'bottom'    => 'Bottom',
                    'both'      => 'Both',
                ),
            ))
            ->add('relatedProduct', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'empty_value' => '-Choose Related Product-',
                'choices' => array(
                    'category'    => 'Category',
                    'brand'       => 'Brand',
                ),
            ))
            ->add('relatedProductMode', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'grid'       => 'Grid',
                    'slider'    => 'Slider',
                ),
            ))
            ->add('productTheme', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    'default'       => 'Default',
                    'family-mart'    => 'Family Mart',
                    'medicine'      => 'Medicine',
                    'alshefa'       => 'Alshefa',
                    'theme-1'       => 'Theme-1',
                    'theme-2'       => 'Theme-2',
                    'theme-3'       => 'Theme-3',
                    'theme-4'       => 'Theme-4',
                    'theme-5'       => 'Theme-5',
                    'theme-6'       => 'Theme-6',
                    'theme-7'       => 'Theme-7',
                    'theme-8'       => 'Theme-8',
                    'theme-9'       => 'Theme-9',
                    'theme-19'       => 'Theme-10',
                ),
            ))
            ->add('file')
            ->add('isOrderPoint')
            ->add('appHomeFeaturePromotion')
            ->add('appHomeFeatureDiscount')
            ->add('appHomeFeatureBrand')
            ->add('appHomeFeatureCategory')
            ->add('isInventoryStock')
            ->add('cashOnDelivery')
            ->add('mobileFilter')
            ->add('cartSearch')
            ->add('cartSearch')
            ->add('orderDirectProcess')
            ->add('appHomeSlider')
            ->add('productDetails')
            ->add('footerCategory')
            ->add('isAdditionalItem')
            ->add('searchCategory')
            ->add('showSidebar')
            ->add('showBrand')
            ->add('showCategory')
            ->add('sidebarBrand')
            ->add('sidebarCategory')
            ->add('sidebarPrice')
            ->add('sidebarSize')
            ->add('sidebarColor')
            ->add('sidebarPromotion')
            ->add('sidebarDiscount')
            ->add('sidebarTag')
            ->add('isPreorder')
            ->add('isColor')
            ->add('customTheme')
            ->add('isSize')
            ->add('printBy')
            ->add('uploadFile')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('printer', 'choice', array(
                'attr'=>array('class'=>'check-list'),
                'expanded'      =>true,
                'multiple'      =>true,
                'choices' => array(
                    'save'          => 'Save',
                    'printer'       => 'Printer',
                    'pos'           => 'Pos Printer',
                ),
            ))
            ->add('vatEnable')
            ->add('vat','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('vatRegNo','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printMarginBottom','text',array('attr'=>array('class'=>'m-wrap numeric span8')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_ecommercebundle_ecommerceconfig';
    }
}
