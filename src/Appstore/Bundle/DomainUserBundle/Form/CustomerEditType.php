<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerEditType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    function __construct( LocationRepository $location)
    {
        $this->location = $location;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Customer name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter customer name'))
                    ))
            )
            ->add('employeeId','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Employee ID'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter employee ID'))
                    ))
            )
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap inputs gender'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('M' => 'M','F' => 'F'),
            ))
            ->add('creditLimit','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Credit limit')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Email address')))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Mobile no')))
            ->add('alternativeMobile','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Alternative mobile no')))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 ','rows'=>8,'placeholder'=>'Enter customer address'))
            )
            ->add('status')
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 m-wrap span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
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
        return 'customer';
    }

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}
