<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\ItemAssurance;
use Setting\Bundle\ToolBundle\Entity\ProductColor;
use Setting\Bundle\ToolBundle\Entity\ProductSize;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Item
 *
 * @ORM\Table("ecommerce_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\ItemRepository")
 */
class Item
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="items" , cascade={"detach","merge"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $ecommerceConfig;

	/**
	 * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="masterProducts" )
	 **/
	private  $category;


	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemSub", mappedBy="item" , cascade={"remove"} )
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private  $itemSubs;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemMetaAttribute", mappedBy="item" , cascade={"remove"}  )
     **/
    private  $itemMetaAttributes;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemKeyValue", mappedBy="item" , cascade={"remove"}  )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private  $itemKeyValues;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemGallery", mappedBy="item" , cascade={"remove"} )
     */
    protected $itemGalleries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\OrderItem", mappedBy="item" , cascade={"remove"} )
     */
    protected $orderItems;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Country", inversedBy="items", cascade={"detach","merge"} )
     * @ORM\JoinColumn(nullable=true)
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemBrand", inversedBy="items")
     */
    protected $brand;
    
    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", inversedBy="ecommerceItem",cascade={"detach","merge"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
     protected $inventoryItem;


    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductColor")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private  $itemColors;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductSize")
     **/
    private  $size;

     /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit")
     **/
    private  $sizeUnit;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Discount",inversedBy="items",cascade={"detach","merge"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $discount;

    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion" ,cascade={"detach","merge"})
     **/
    private  $tag;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", cascade={"detach","merge"})
     **/
    private  $promotion;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit",cascade={"detach","merge"})
     **/
    private  $productUnit;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ItemAssurance",cascade={"detach","merge"})
     **/
    private  $itemAssurance;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable = true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="itemGroup", type="string", length=100, nullable = true)
     */
    private $itemGroup;


    /**
     * @var string
     *
     * @ORM\Column(name="webName", type="string", length=255, nullable = true)
     */
    private $webName;


     /**
     * @var string
     *
     * @ORM\Column(name="productBengalName", type="string", length=255, nullable = true)
     */
    private $productBengalName;


    /**
     * @var string
     *
     * @ORM\Column(name="nameBn", type="string", length=255, nullable = true)
     */
    private $nameBn;


    /**
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="parentRelationField", value="category"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-")
     *      })
     * }, fields={"name","code"})
     * @Doctrine\ORM\Mapping\Column(unique=false, nullable = true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=50,nullable = true)
     */
    private $sku;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private $code;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable = true)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="masterQuantity", type="integer", nullable = true)
     */
    private $masterQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="purchasePrice", type="decimal", nullable = true)
     */
    private $purchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="subTotalPurchasePrice", type="decimal", nullable = true)
     */
    private $subTotalPurchasePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float",  nullable=true)
     */
    private $salesPrice = 0;
    /**
     * @var string
     *
     * @ORM\Column(name="discountPrice", type="decimal", nullable = true)
     */
    private $discountPrice;

	/**
     * @var string
     *
     * @ORM\Column(name="webPrice", type="decimal", nullable = true)
     */
    private $webPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="overHeadCost", type="decimal", nullable = true)
     */
    private $overHeadCost;

     /**
     * @var string
     *
     * @ORM\Column(name="imageDefaultSource", type="string", nullable = true)
     */
    private $imageDefaultSource = "product";

    /**
     * @var boolean
     *
     * @ORM\Column(name="isWeb", type="boolean", nullable = true)
     */
    private $isWeb = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isFeatureBrand", type="boolean", nullable = true)
     */
    private $isFeatureBrand = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isFeatureCategory", type="boolean", nullable = true)
     */
    private $isFeatureCategory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable = true)
     */
    private $status = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="quantityApplicable", type="boolean", nullable = true)
     */
    private $quantityApplicable = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="maxQuantity", type="integer", nullable = true)
     */
    private $maxQuantity = 100;

    /**
     * @var integer
     *
     * @ORM\Column(name="minQuantity", type="integer", nullable = true)
     */
    private $minQuantity = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseQuantity", type="integer", nullable = true)
     */
    private $purchaseQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesQuantity", type="integer", nullable = true)
     */
    private $salesQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseReturnQuantity", type="integer", nullable = true)
     */
    private $purchaseReturnQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesReturnQuantity", type="integer", nullable = true)
     */
    private $salesReturnQuantity;


    /**
     * @var integer
     *
     * @ORM\Column(name="remainingQuantity", type="integer", nullable = true)
     */
    private $remainingQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="damageQuantity", type="integer", nullable = true)
     */
    private $damageQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="adjustmentQuantity", type="integer", nullable = true)
     */
    private $adjustmentQuantity;



    /**
     * @var boolean
     *
     * @ORM\Column(name="preOrder", type="boolean", nullable = true)
     */
    private $preOrder = false;


     /**
     * @var boolean
     *
     * @ORM\Column(name="subProduct", type="boolean", nullable = true)
     */
    private $subProduct = false;


    /**
     * @var string
     *
     * @ORM\Column(name="warningLabel", type="string", length=10 , nullable = true)
     */
    private $warningLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="warningText", type="string", length=255 , nullable = true)
     */
    private $warningText;


    /**
     * @var array()
     *
     * @ORM\Column(name="ageGroup", type="array", nullable = true)
     */
    private $ageGroup;


    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10 , nullable = true)
     */
    private $gender;



    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length= 30 , nullable = true)
     */
    private $source = "ecommerce";


    /**
     * @var boolean
     *
     * @ORM\Column(name="isDelete", type="boolean", nullable = true)
     */
    private $isDelete;

    /**
     * @var text
     *
     * @ORM\Column(name="shortContent", type="text", nullable=true)
     */
    private $shortContent;


      /**
     * @var text
     *
     * @ORM\Column(name="shortContenBnt", type="text", nullable=true)
     */
    private $shortContentBn;


     /**
     * @var text
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var text
     *
     * @ORM\Column(name="contentBn", type="text", nullable=true)
     */
    private $contentBn;



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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function removeGoodsItems($group)
    {
        //optionally add a check here to see that $group exists before removing it.
       // return $this->itemSubs->removeElement($group);
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Item
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Item
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
     * Set purchasePrice
     *
     * @param string $purchasePrice
     *
     * @return Item
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice
     *
     * @return string
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

	
    /**
     * @param mixed $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

 
    /**
     * @return string
     */
    public function getSubTotalPurchasePrice()
    {
        return $this->subTotalPurchasePrice;
    }

    /**
     * @param string $subTotalPurchasePrice
     */
    public function setSubTotalPurchasePrice($subTotalPurchasePrice)
    {
        $this->subTotalPurchasePrice = $subTotalPurchasePrice;
    }


    
    /**
     * @return boolean
     */
    public function getIsWeb()
    {
        return $this->isWeb;
    }

    /**
     * @param boolean $isWeb
     */
    public function setIsWeb($isWeb)
    {
        $this->isWeb = $isWeb;
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
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return ItemBrand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param ItemBrand $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return ItemMetaAttribute
     */
    public function getItemMetaAttributes()
    {
        return $this->itemMetaAttributes;
    }

    /**
     * @return ItemGallery
     */
    public function getItemGalleries()
    {
        return $this->itemGalleries;
    }

    /**
     * @return string
     */
    public function getWebName()
    {
        return $this->webName;
    }

    /**
     * @param string $webName
     */
    public function setWebName($webName)
    {
        $this->webName = $webName;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * Sets file.
     *
     * @param Item $file
     */
    public function setFile($file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Item
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }


    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'uploads/domain/'.$this->getEcommerceConfig()->getGlobalOption()->getId().'/ecommerce/product/';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $filename = date('YmdHmi') . "-" . $this->getFile()->getClientOriginalName();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->path = $filename;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }


    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    function deleteImageDirectory()
    {
        $dir =$this->getUploadDir();
        $a = new Filesystem();
        $a->remove($dir);
        $a->mkdir($dir);

    }


    /**
     * @return boolean
     */
    public function getSubProduct()
    {
        return $this->subProduct;
    }

    /**
     * @param boolean $subProduct
     */
    public function setSubProduct($subProduct)
    {
        $this->subProduct = $subProduct;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * Inventory
     * FoodProduct
     * VirtualProduct
     */
    public function setSource($source)
    {
        $this->source = $source;
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
     * @return ItemKeyValue
     */
    public function getItemKeyValues()
    {
        return $this->itemKeyValues;
    }

    public function __clone() {
        $this->id = null;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @return Discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param Discount $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return ProductColor
     */
    public function getItemColors()
    {
        return $this->itemColors;
    }

    /**
     * @param ProductColor $itemColors
     */
    public function setItemColors($itemColors)
    {
        $this->itemColors = $itemColors;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
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
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param mixed $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return string
     */
    public function getOverHeadCost()
    {
        return $this->overHeadCost;
    }

    /**
     * @param string $overHeadCost
     */
    public function setOverHeadCost($overHeadCost)
    {
        $this->overHeadCost = $overHeadCost;
    }

    /**
     * @return int
     */
    public function getMasterQuantity()
    {
        return $this->masterQuantity;
    }

    /**
     * @param int $masterQuantity
     */
    public function setMasterQuantity($masterQuantity)
    {
        $this->masterQuantity = $masterQuantity;
    }

    /**
     * @return array
     */
    public function getAgeGroup()
    {
        return $this->ageGroup;
    }

    /**
     * @param array $ageGroup
     */
    public function setAgeGroup($ageGroup)
    {
        $this->ageGroup = $ageGroup;
    }
   
    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getSTRPadCode()
    {
        $code = str_pad($this->getCode(),6, '0', STR_PAD_LEFT);
        return $code;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getWarningLabel()
    {
        return $this->warningLabel;
    }

    /**
     * @param string $warningLabel
     */
    public function setWarningLabel($warningLabel)
    {
        $this->warningLabel = $warningLabel;
    }

    /**
     * @return string
     */
    public function getWarningText()
    {
        return $this->warningText;
    }

    /**
     * @param string $warningText
     */
    public function setWarningText($warningText)
    {
        $this->warningText = $warningText;
    }

    /**
     * @return ProductUnit
     */
    public function getProductUnit()
    {
        return $this->productUnit;
    }

    /**
     * @param mixed $productUnit
     */
    public function setProductUnit($productUnit)
    {
        $this->productUnit = $productUnit;
    }

    /**
     * @return bool
     */
    public function getPreOrder()
    {
        return $this->preOrder;
    }

    /**
     * @param bool $preOrder
     */
    public function setPreOrder($preOrder)
    {
        $this->preOrder = $preOrder;
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
	 * @return ItemSub
	 */
	public function getItemSubs() {
		return $this->itemSubs;
	}

	/**
	 * @return Category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param Category $category
	 */
	public function setCategory( $category ) {
		$this->category = $category;
	}

	/**
	 * @return string
	 */
	public function getWebPrice(): string {
		return $this->webPrice;
	}

	/**
	 * @param string $webPrice
	 */
	public function setWebPrice( string $webPrice ) {
		$this->webPrice = $webPrice;
	}

    /**
     * @return ItemAssurance
     */
    public function getItemAssurance()
    {
        return $this->itemAssurance;
    }

    /**
     * @param ItemAssurance $itemAssurance
     */
    public function setItemAssurance($itemAssurance)
    {
        $this->itemAssurance = $itemAssurance;
    }

    /**
     * @return text
     */
    public function getShortContent()
    {
        return $this->shortContent;
    }

    /**
     * @param text $shortContent
     */
    public function setShortContent($shortContent)
    {
        $this->shortContent = $shortContent;
    }

    /**
     * @return string
    */
    public function getImageDefaultSource()
    {
        return $this->imageDefaultSource;
    }

    /**
     * @param string $imageDefaultSource
     */
    public function setImageDefaultSource($imageDefaultSource)
    {
        $this->imageDefaultSource = $imageDefaultSource;
    }

    /**
     * @return bool
     */
    public function getQuantityApplicable()
    {
        return $this->quantityApplicable;
    }

    /**
     * @param bool $quantityApplicable
     */
    public function setQuantityApplicable($quantityApplicable)
    {
        $this->quantityApplicable = $quantityApplicable;
    }

    /**
     * @return MedicineBrand
     */
    public function getMedicine()
    {
        return $this->medicine;
    }

    /**
     * @param MedicineBrand $medicine
     */
    public function setMedicine($medicine)
    {
        $this->medicine = $medicine;
    }

    /**
     * @return string
     */
    public function getItemGroup()
    {
        return $this->itemGroup;
    }

    /**
     * @param string $itemGroup
     */
    public function setItemGroup($itemGroup)
    {
        $this->itemGroup = $itemGroup;
    }

    /**
     * @return bool
     */
    public function getIsFeatureBrand()
    {
        return $this->isFeatureBrand;
    }

    /**
     * @param bool $isFeatureBrand
     */
    public function setIsFeatureBrand($isFeatureBrand)
    {
        $this->isFeatureBrand = $isFeatureBrand;
    }

    /**
     * @return bool
     */
    public function getIsFeatureCategory()
    {
        return $this->isFeatureCategory;
    }

    /**
     * @param bool $isFeatureCategory
     */
    public function setIsFeatureCategory($isFeatureCategory)
    {
        $this->isFeatureCategory = $isFeatureCategory;
    }

    /**
     * @return AccountVendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param AccountVendor $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return int
     */
    public function getMaxQuantity()
    {
        return $this->maxQuantity;
    }

    /**
     * @param int $maxQuantity
     */
    public function setMaxQuantity($maxQuantity)
    {
        $this->maxQuantity = $maxQuantity;
    }

    /**
     * @return int
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * @param int $minQuantity
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = $minQuantity;
    }

    /**
     * @return ProductUnit
     */
    public function getSizeUnit()
    {
        return $this->sizeUnit;
    }

    /**
     * @param ProductUnit $sizeUnit
     */
    public function setSizeUnit($sizeUnit)
    {
        $this->sizeUnit = $sizeUnit;
    }

    /**
     * @return string
     */
    public function getProductBengalName()
    {
        return $this->productBengalName;
    }

    /**
     * @param string $productBengalName
     */
    public function setProductBengalName($productBengalName)
    {
        $this->productBengalName = $productBengalName;
    }

    public function getProductName()
    {
        $name = "";
        $language = $this->getEcommerceConfig()->getShowBengal();
        if($language === "englishbangla" and !empty($this->getProductBengalName())){
            $name = $this->getWebName() .' ('.$this->getProductBengalName().') ';
        }elseif($language === "english-bangla" and !empty($this->getProductBengalName())) {
            $name = $this->getWebName() . ' -' . $this->getProductBengalName();
        }elseif($language === "bangla" and !empty($this->getProductBengalName())) {
            $name = $this->getProductBengalName();
        }else{
            $name = $this->getWebName();
        }
        return $name;
    }

    /**
     * @return int
     */
    public function getPurchaseQuantity()
    {
        return $this->purchaseQuantity;
    }

    /**
     * @param int $purchaseQuantity
     */
    public function setPurchaseQuantity($purchaseQuantity)
    {
        $this->purchaseQuantity = $purchaseQuantity;
    }

    /**
     * @return int
     */
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param int $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
    }

    /**
     * @return int
     */
    public function getPurchaseReturnQuantity()
    {
        return $this->purchaseReturnQuantity;
    }

    /**
     * @param int $purchaseReturnQuantity
     */
    public function setPurchaseReturnQuantity($purchaseReturnQuantity)
    {
        $this->purchaseReturnQuantity = $purchaseReturnQuantity;
    }

    /**
     * @return int
     */
    public function getSalesReturnQuantity()
    {
        return $this->salesReturnQuantity;
    }

    /**
     * @param int $salesReturnQuantity
     */
    public function setSalesReturnQuantity($salesReturnQuantity)
    {
        $this->salesReturnQuantity = $salesReturnQuantity;
    }

    /**
     * @return int
     */
    public function getRemainingQuantity()
    {
        return $this->remainingQuantity;
    }

    /**
     * @param int $remainingQuantity
     */
    public function setRemainingQuantity($remainingQuantity)
    {
        $this->remainingQuantity = $remainingQuantity;
    }

    /**
     * @return int
     */
    public function getDamageQuantity()
    {
        return $this->damageQuantity;
    }

    /**
     * @param int $damageQuantity
     */
    public function setDamageQuantity($damageQuantity)
    {
        $this->damageQuantity = $damageQuantity;
    }

    /**
     * @return int
     */
    public function getAdjustmentQuantity()
    {
        return $this->adjustmentQuantity;
    }

    /**
     * @param int $adjustmentQuantity
     */
    public function setAdjustmentQuantity($adjustmentQuantity)
    {
        $this->adjustmentQuantity = $adjustmentQuantity;
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

    /**
     * @return \Appstore\Bundle\InventoryBundle\Entity\Item
     */
    public function getInventoryItem()
    {
        return $this->inventoryItem;
    }

    /**
     * @param mixed $inventoryItem
     */
    public function setInventoryItem($inventoryItem)
    {
        $this->inventoryItem = $inventoryItem;
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->isDelete;
    }

    /**
     * @param bool $isDelete
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;
    }

    /**
     * @return text
     */
    public function getShortContentBn()
    {
        return $this->shortContentBn;
    }

    /**
     * @param text $shortContentBn
     */
    public function setShortContentBn($shortContentBn)
    {
        $this->shortContentBn = $shortContentBn;
    }

    /**
     * @return text
     */
    public function getContentBn()
    {
        return $this->contentBn;
    }

    /**
     * @param text $contentBn
     */
    public function setContentBn($contentBn)
    {
        $this->contentBn = $contentBn;
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
     * @return MedicineStock
     */
    public function getMedicineItem()
    {
        return $this->medicineItem;
    }

    /**
     * @param MedicineStock $medicineItem
     */
    public function setMedicineItem($medicineItem)
    {
        $this->medicineItem = $medicineItem;
    }


}

