<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItem
 *
 * @ORM\Table("ems_order_wishlist")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\WishlistRepository")
 */
class Wishlist
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer")
     **/
    private  $customer;


     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item")
     **/
    private  $item;


    /**
     * @var string
     *
     * @ORM\Column(name="itemSource", type="string" , length = 30, nullable=true)
     */
    private $itemSource = 'online';

     /**
     * @var string
     *
     * @ORM\Column(name="itemName", type="string", nullable=true)
     */
    private $itemName;

      /**
     * @var string
     *
     * @ORM\Column(name="imagePath", type="string", nullable=true)
     */
    private $imagePath;

     /**
     * @var string
     *
     * @ORM\Column(name="brandName", type="string", nullable=true)
     */
    private $brandName;

    /**
     * @var string
     *
     * @ORM\Column(name="categoryName", type="string", nullable=true)
     */
    private $categoryName;

    /**
     * @var string
     *
     * @ORM\Column(name="unitName", type="string" , nullable=true)
     */
    private $unitName;


     /**
     * @var string
     *
     * @ORM\Column(name="size", type="string" , nullable=true)
     */
    private $size;


    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string" , nullable=true)
     */
    private $color;


    /**
     * @var integer
     *
     * @ORM\Column(name="orderId", type="integer" ,nullable=true)
     */
    private $orderId;

      /**
     * @var integer
     *
     * @ORM\Column(name="orderItemId", type="integer" ,nullable=true)
     */
    private $orderItemId;

     /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint" ,nullable=true)
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="discountPrice", type="float", nullable=true)
     */
    private $discountPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float",nullable=true)
     */
    private $subTotal;

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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderItem
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
     * Set price
     *
     * @param float $price
     *
     * @return OrderItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set subTotal
     *
     * @param float $subTotal
     *
     * @return OrderItem
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    /**
     * Get subTotal
     *
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
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


    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function getItemSource()
    {
        return $this->itemSource;
    }

    /**
     * @param string $itemSource
     * online
     * stock
     * dims
     * anonymous
     */

    public function setItemSource($itemSource)
    {
        $this->itemSource = $itemSource;
    }

    /**
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @param string $itemName
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * @param string $brandName
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @param string $categoryName
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    }

    /**
     * @return string
     */
    public function getUnitName()
    {
        return $this->unitName;
    }

    /**
     * @param string $unitName
     */
    public function setUnitName($unitName)
    {
        $this->unitName = $unitName;
    }

    /**
     * @return float
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param float $discountPrice
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * @param int $orderItemId
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;
    }

    /**
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param string $imagePath
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }




}

