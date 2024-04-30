<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PreOrderItem
 *
 * @ORM\Table("ems_pre_order_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\PreOrderItemRepository")
 */
class PreOrderItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", inversedBy="preOrderItems"  )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $preOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="currencyType", type="string", length=20, nullable = true)
     */
    private $currencyType;


    /**
     * @var float
     *
     * @ORM\Column(name="unitPrice", type="float",  nullable=true)
     */
    private $unitPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float",  nullable=true)
     */
    private $subTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="convertRate", type="float",  nullable=true)
     */
     private $convertRate;

     /**
     * @var float
     *
     * @ORM\Column(name="convertUnitPrice", type="float",  nullable=true)
     */
     private $convertUnitPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="convertSubTotal", type="float",  nullable=true)
     */
    private $convertSubTotal;


    /**
     * @var text
     *
     * @ORM\Column(name="url", type="text",  nullable = true)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer" , nullable = true)
     */
    private $quantity = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="shippingCharge", type="float", nullable = true)
     */
    private $shippingCharge;

    /**
     * @var float
     *
     * @ORM\Column(name="convertTotal", type="float" , nullable = true)
     */
    private $convertTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text", nullable = true)
     */
    private $details;

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
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint" ,length=1 ,nullable = true)
     */
    private $status = 0;


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
     * @return PreOrderItem
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
     * Set url
     *
     * @param string $url
     *
     * @return PreOrderItem
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PreOrderItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return PreOrderItem
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return mixed
     */
    public function getPreOrder()
    {
        return $this->preOrder;
    }

    /**
     * @param mixed $preOrder
     */
    public function setPreOrder($preOrder)
    {
        $this->preOrder = $preOrder;
    }

    /**
     * @return float
     */
    public function getShippingCharge()
    {
        return $this->shippingCharge;
    }

    /**
     * @param float $shippingCharge
     */
    public function setShippingCharge($shippingCharge)
    {
        $this->shippingCharge = $shippingCharge;
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
    public function getCurrencyType()
    {
        return $this->currencyType;
    }

    /**
     * @param string $currencyType
     */
    public function setCurrencyType($currencyType)
    {
        $this->currencyType = $currencyType;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return float
     */
    public function getConvertTotal()
    {
        return $this->convertTotal;
    }

    /**
     * @param float $convertTotal
     */
    public function setConvertTotal($convertTotal)
    {
        $this->convertTotal = $convertTotal;
    }

    /**
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param float $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return float
     */
    public function getConvertUnitPrice()
    {
        return $this->convertUnitPrice;
    }

    /**
     * @param float $convertUnitPrice
     */
    public function setConvertUnitPrice($convertUnitPrice)
    {
        $this->convertUnitPrice = $convertUnitPrice;
    }

    /**
     * @return float
     */
    public function getConvertSubTotal()
    {
        return $this->convertSubTotal;
    }

    /**
     * @param float $convertSubTotal
     */
    public function setConvertSubTotal($convertSubTotal)
    {
        $this->convertSubTotal = $convertSubTotal;
    }

    /**
     * @return float
     */
    public function getConvertRate()
    {
        return $this->convertRate;
    }

    /**
     * @param float $convertRate
     */
    public function setConvertRate($convertRate)
    {
        $this->convertRate = $convertRate;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}

