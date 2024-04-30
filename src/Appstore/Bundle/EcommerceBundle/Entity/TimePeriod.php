<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * ItemBrand
 *
 * @ORM\Table("ecommerce_time_period")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\TimePeriodRepository")
 */
class TimePeriod
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="timePeriods" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $ecommerceConfig;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="timePeriod")
     **/
    private $orders;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


     /**
     * @var string
     *
     * @ORM\Column(name="nameBn", type="string", length=255)
     */
    private $nameBn;


    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255)
     */
    private $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;



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
     * Set name
     *
     * @param string $name
     *
     * @return ItemBrand
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }



    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return ItemBrand
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }


    /**
     * Set status1
     *
     * @param boolean $status1
     *
     * @return ItemBrand
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }


	/**
	 * @return EcommerceConfig
	 */
	public function getEcommerceConfig() {
		return $this->ecommerceConfig;
	}

	/**
	 * @param EcommerceConfig $ecommerceConfig
	 */
	public function setEcommerceConfig( $ecommerceConfig ) {
		$this->ecommerceConfig = $ecommerceConfig;
	}

    /**
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return string
     */
    public function getNameBn()
    {
        return $this->nameBn;
    }

    /**
     * @param string $nameBn
     */
    public function setNameBn($nameBn)
    {
        $this->nameBn = $nameBn;
    }


}

