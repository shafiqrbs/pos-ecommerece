<?php

namespace Setting\Bundle\ToolBundle\Entity;


use Appstore\Bundle\InventoryBundle\Entity\Product;
use Appstore\Bundle\InventoryBundle\Entity\StockItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineMinimumStock;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * ProductUnit
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\ProductUnitRepository")
 */

class ProductUnit
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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", mappedBy="productUnit")
     */
    protected $masterProducts;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\GoodsItem", mappedBy="productUnit")
     */
    protected $goodsItem;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item", mappedBy="productUnit")
     */
    protected $item;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", mappedBy="productUnit")
     */
    protected $purchaseVendorItem;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="unit")
     */
    protected $stockItems;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="nameBn", type="string", length=255,nullable=true)
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
    private $status=true;


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
     * @return ProductUnit
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
     * @return ProductUnit
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
     * Set status
     *
     * @param boolean $status
     * @return ProductUnit
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
     * @return Product
     */
    public function getMasterProducts()
    {
        return $this->masterProducts;
    }

    /**
     * @return StockItem
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }


    /**
     * @return MedicineStock
     */
    public function getMedicineStocks()
    {
        return $this->medicineStocks;
    }

    /**
     * @return MedicineMinimumStock
     */
    public function getMedicineMinimumStock()
    {
        return $this->medicineMinimumStock;
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

