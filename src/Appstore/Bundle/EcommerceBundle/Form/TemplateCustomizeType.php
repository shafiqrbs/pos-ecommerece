<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TemplateCustomizeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mobileShowLogo')
            ->add('otpLogin')
            ->add('poweredBy','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter powered by')))
            ->add('introTitle','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter intro slogan')))
            ->add('contactEmail','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact email')))
            ->add('facebookPixel','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Google Analytic ID')))
            ->add('fbMessenger','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Facebook Messenger ID')))
            ->add('tawk','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Tawk ID')))
            ->add('liveChat','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Live Chat ID')))
            ->add('googleAnalytic','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter google analytic ID')))
            ->add('powr','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter POWR ID')))
            ->add('siteTermsCondition','textarea', array('attr'=>array('class'=>'m-wrap span12','row' => 12,'placeholder'=>'Enter terms & condition')))
            ->add('siteTermsConditionbn','textarea', array('attr'=>array('class'=>'m-wrap span12','row' => 12,'placeholder'=>'Enter terms & condition')))
            ->add('siteLanguage', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array('english' => 'English',  'bangoli' => 'Bangoli'),
            ))

            ->add('socialIconType', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'choices' => array(
                    '' => '---Select Icon Type ---',
                    'icon-fadein' => 'Icon Fadein',
                    'icon-loop' => 'Icon Loop',
                    'icon-square' => 'Icon Square',
                    'icon-circle' => 'Icon Circle',
                ),
            ))
            ->add('playStore','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Play Store url')))
            ->add('appleStore','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Apple store url')))
            ->add('mobileHeaderBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('bodyColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('inputBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('inputBgFocusColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileFooterBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileFooterAnchorBg','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appNoticeColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appCloseColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
             ->add('appSuccessColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
             ->add('mobileFooterAnchorColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileFooterAnchorColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('mobileMenuBgColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileMenuBgColorHover','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileMenuLiAColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('mobileMenuLiAHoverColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))


            ->add('appBarColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))

            ->add('appPrimaryColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appSecondaryColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appTextTitle','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appBorderColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appMoreColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appNegativeColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appPositiveColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appCartColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appTextColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('androidIconColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
             ->add('appDiscountColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appBorderActiveColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('appBorderInactiveColor','text', array('attr'=>array(
                'class'=>'m-wrap span10 colorpicker-default',
                'placeholder'=>'')
            ))
        ;
      //  $builder->add('globalOption', new SocialIconType());

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'templatecustomize';
    }
}
