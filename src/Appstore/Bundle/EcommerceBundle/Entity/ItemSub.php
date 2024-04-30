<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\ProductColor;
use Setting\Bundle\ToolBundle\Entity\ProductSize;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;

/**
 * GoodsItem
 *
 * @ORM\Table("ecommerce_item_sub")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\ItemSubRepository")
 */
class ItemSub
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item", inversedBy="itemSubs" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $item;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductSize")
     */
    protected $size;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductColor")
     */
    protected $colors;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit")
     **/
    private  $productUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable = true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="integer", nullable = true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="salesQuantity", type="integer", nullable = true)
     */
    private $salesQuantity;

     /**
     * @var string
     *
     * @ORM\Column(name="salesPrice", type="decimal", nullable = true)
     */
    private $salesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="purchasePrice", type="decimal", nullable = true)
     */
    private $purchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="discountPrice", type="decimal", nullable = true)
     */
    private $discountPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="masterItem", type="boolean", nullable=true)
     */
    private $masterItem = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
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
     * @return ProductSize
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param ProductSize $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param string $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return boolean
     */
    public function getMasterItem()
    {
        return $this->masterItem;
    }

    /**
     * @param boolean $masterItem
     */
    public function setMasterItem($masterItem)
    {
        $this->masterItem = $masterItem;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @return string
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param string $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return ProductColor
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * @param ProductColor $colors
     */
    public function setColors($colors)
    {
        $this->colors = $colors;
    }

    /**
     * @return string
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param string $discountPrice
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
    }

    /**
     * @return string
     */
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param string $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
    }

    /**
     * @return ProductUnit
     */
    public function getProductUnit()
    {
        return $this->productUnit;
    }

    /**
     * @param ProductUnit $productUnit
     */
    public function setProductUnit($productUnit)
    {
        $this->productUnit = $productUnit;
    }

	/**
	 * @return Item
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * @param Item $item
	 */
	public function setItem( $item ) {
		$this->item = $item;
	}

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}

