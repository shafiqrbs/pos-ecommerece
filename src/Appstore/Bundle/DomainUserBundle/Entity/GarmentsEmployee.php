<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * GarmentsEmployee
 *
 * @ORM\Table("domain_garments_employee")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\GarmentsEmployeeRepository")
 */
class GarmentsEmployee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 **/
	protected $globalOption;



    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


	/**
     * @var string
     *
     * @ORM\Column(name="employee_code", type="string", unique=true, length=255, nullable=true)
     *
     */
    private $employeeCode;


    /**
     * @var string
     *
     * @ORM\Column(name="customer_id", type="string" , length=255, nullable=true)
     *
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string" , length=255, nullable=true)
     *
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string" , length=255, nullable=true)
     *
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string" , length=255, nullable=true)
     *
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string" , length=255, nullable=true)
     *
     */
    private $designation;


     /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string" , length=50, nullable=true)
     *
     */
    private $gender;


    /**
     * @var float
     *
     * @ORM\Column(name="credit_limit", type="float" ,  nullable=true)
     *
     */
    private $creditLimit;



    /**
     * @var boolean
     *
     * @ORM\Column(name="archive", type="boolean" )
     */
    private $archive = 0;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }


    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

    /**
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return string
     */
    public function getEmployeeCode()
    {
        return $this->employeeCode;
    }

    /**
     * @param string $employeeCode
     */
    public function setEmployeeCode($employeeCode)
    {
        $this->employeeCode = $employeeCode;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * @param string $designation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return float
     */
    public function getCreditLimit()
    {
        return $this->creditLimit;
    }

    /**
     * @param float $creditLimit
     */
    public function setCreditLimit($creditLimit)
    {
        $this->creditLimit = $creditLimit;
    }


}
