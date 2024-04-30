<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Appstore\Bundle\DomainUserBundle\Repository\CustomerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class MemberEditProfileType extends AbstractType
{


    /** @var $em CustomerRepository */

    private $em;



    function __construct(CustomerRepository $em)
    {
        $this->em = $em;

    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your full name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter mobile number', 'data-original-title' =>'Must be use personal mobile number.' , 'data-trigger' => 'hover'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter address details'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('permanentAddress','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter permanent address')))
            ->add('fatherName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your father name')))
            ->add('fatherDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter father occupation')))
            ->add('motherName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your mother name')))
            ->add('motherDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter mother occupation')))
            ->add('ssc','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter SSS passing year')))
             ->add('hsc','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter HSC passing year')))
            ->add('spouseName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your spouse name')))
            ->add('spouseOccupation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your spouse occupation')))
            ->add('spouseDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your spouse occupation')))
            ->add('additionalPhone','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your email address')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter national id card no')))
            ->add('facebookId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter facebook ID')))
            ->add('nid','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter national id card no','autoComplete'=>false)))
            ->add('memberDesignation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your occupation')))
            ->add('dob','birthday', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('about','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>'8')))
            ->add('profession','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter member occupation'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter member occupation')),
                )
            ))
            ->add('religion', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose a Religion---',
                'choices' => array(
                    'Muslim' => 'Muslim',
                    'Hinduism' => 'Hinduism',
                    'Buddhism' => 'Buddhism',
                    'Christianity' => 'Christianity',
                    'Other religions' => 'Other religions',
                ),
            ))
            ->add('batchYear', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose a study year---',
                'choices' => $this->batchYearChoiceList(),
            ))
            ->add('studentBatch', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select student batch'))),
                'empty_value' => '---Choose a Batch---',
                'choices' => $this->studentBatchChoiceList(),
            ))

            ->add('maritalStatus', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---Choose a Marital Status---',
                'choices' => array('Married' => 'Married','Single' => 'Single'),

            ))
            ->add('country', 'entity', array(
                'required'    => false,
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter present living country')),
                ),
                'property' => 'name',
                'empty_value' => '---Select Present Country---',
                'attr'=>array('class'=>'select2 span11 customer-input'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Country',

            ))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---Choose a Blood Group---',
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),

            ))
            ->add('file', 'file',array(
                'required' => true,
                'constraints' =>array(
                    new File(array(
                        'maxSize' => '1M',
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer'
        ));
    }

    public function getName()
    {
        return 'manage_profile';
    }

    public function studentBatchChoiceList()
    {
        return $syndicateTree = $this->em->studentBatchChoiceList();

    }

    public function batchYearChoiceList()
    {
        return $syndicateTree = $this->em->batchYearChoiceList();

    }


}