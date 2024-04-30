<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Core\UserBundle\Doctrine\DQL\Date;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\LocationBundle\Entity\Country;
use Setting\Bundle\LocationBundle\Entity\Location;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 *
 * @ORM\Table(name="customer_import")
 * @ORM\Entity()
 */
class CustomerImport
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
     * @var string
     *
     * @ORM\Column(name="employeeCode", type="string",  nullable=true)
     */
    private $employeeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="employeeName", type="string",  nullable=true)
     */
    private $employeeName;


    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string",  nullable=true)
     */
    private $gender;


     /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string",  nullable=true)
     */
    private $designation;


     /**
     * @var string
     *
     * @ORM\Column(name="dob", type="string",  nullable=true)
     */
    private $dob;


    /**
     * @var string
     *
     * @ORM\Column(name="section", type="string",  nullable=true)
     */
    private $section;


     /**
     * @var string
     *
     * @ORM\Column(name="line", type="string",  nullable=true)
     */
    private $line;

    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string",  nullable=true)
     */
    private $department;


    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string",  nullable=true)
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string",  nullable=true)
     */
    private $category;


    /**
     * @var string
     *
     * @ORM\Column(name="grade", type="string",  nullable=true)
     */
    private $grade;


     /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string",  nullable=true)
     */
    private $mobile;

     /**
     * @var string
     *
     * @ORM\Column(name="creditLimit", type="string",  nullable=true)
     */
    private $creditLimit;


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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    /**
     * @param string $employeeName
     */
    public function setEmployeeName($employeeName)
    {
        $this->employeeName = $employeeName;
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
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param string $dob
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
    }

    /**
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param string $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param string $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param string $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
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
    public function getCreditLimit()
    {
        return $this->creditLimit;
    }

    /**
     * @param string $creditLimit
     */
    public function setCreditLimit($creditLimit)
    {
        $this->creditLimit = $creditLimit;
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




}

