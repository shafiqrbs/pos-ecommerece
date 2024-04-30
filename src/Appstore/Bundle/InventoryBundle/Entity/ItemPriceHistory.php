<?php

namespace Appstore\Bundle\InventoryBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * ItemSize
 *
 * @ORM\Table(name="inv_item_price_history")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\ItemPriceHistoryRepository")
 */


class ItemPriceHistory
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="size" )
     **/
    private  $inventoryConfig;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item")
     */
    protected $item;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="purchase" )
     **/
    private  $createdBy;

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
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float", nullable=true)
     */
    private $salesPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="avgSalesPrice", type="float", nullable=true)
     */
    private $avgSalesPrice;


    /**
     * @var float
     *
     * @ORM\Column(name="discountPrice", type="float", nullable=true)
     */
    private $discountPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="avgPurchasePrice", type="float", nullable=true)
     */
    private $avgPurchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", nullable=true)
     */
    private $mode;


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
     * @return mixed
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param mixed $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param float $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    /**
     * @return float
     */
    public function getAvgSalesPrice()
    {
        return $this->avgSalesPrice;
    }

    /**
     * @param float $avgSalesPrice
     */
    public function setAvgSalesPrice($avgSalesPrice)
    {
        $this->avgSalesPrice = $avgSalesPrice;
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
     * @return float
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param float $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return float
     */
    public function getAvgPurchasePrice()
    {
        return $this->avgPurchasePrice;
    }

    /**
     * @param float $avgPurchasePrice
     */
    public function setAvgPurchasePrice($avgPurchasePrice)
    {
        $this->avgPurchasePrice = $avgPurchasePrice;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }


}

