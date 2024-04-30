<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\AppModule;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EcommerceConfig
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\EcommerceConfigRepository")
 */
class EcommerceConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="ecommerceConfig")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    private $globalOption;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\AppModule")
     **/
    private $stockApplication;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemImport", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $itemImports;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Template", mappedBy="ecommerceConfig"  , cascade={"persist", "remove"} )
     **/
    private  $templates;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", mappedBy="ecommerceConfig"  , cascade={"persist", "remove"} )
     **/
    private  $preOrders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $orders;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\DeliveryLocation", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $locations;


      /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\TimePeriod", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $timePeriods;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Coupon", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $coupons;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Discount", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $discounts;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemAttribute", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $itemAttributes;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $promotions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemBrand", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $brands;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemCategoryGrouping", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $categoryGrouping;

    /**
     * @ORM\OneToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Category", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $categories;

    /**
     * @var string
     *
     * @ORM\Column(name="pickupLocation", type="text", nullable = true)
     */
    private $pickupLocation;

    /**
     * @var string
     *
     * @ORM\Column(name="menuType", type="text", nullable = true)
     */
    private $menuType = 'Mega';

    /**
     * @var string
     *
     * @ORM\Column(name="titleBar", type="text", nullable = true)
     */
    private $titleBar = 'top';

    /**
     * @var string
     *
     * @ORM\Column(name="paginationShow", type="text", nullable = true)
     */
    private $paginationShow = 'bottom';

    /**
     * @var float
     *
     * @ORM\Column(name="shippingCharge", type="float", nullable = true)
     */
    private $shippingCharge = 100;

    /**
     * @var integer
     *
     * @ORM\Column(name="perPage", type="smallint", nullable = true)
     */
     private $perPage = 16;

     /**
     * @var integer
     *
     * @ORM\Column(name="perColumn", type="smallint", nullable = true)
     */
     private $perColumn = 4;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="text",  length=2, nullable = true)
     */
     private $currency = "৳";

    /**
     * @var string
     *
     * @ORM\Column(name="cartProcess", type="text",  length=50, nullable = true)
     */
     private $cartProcess = "single";

    /**
     * @var string
     *
     * @ORM\Column(name="relatedProduct", type="text",  length=50, nullable = true)
     */
     private $relatedProduct;

     /**
     * @var string
     *
     * @ORM\Column(name="productMode", type="text",  length=50, nullable = true)
     */
     private $productMode = 'grid';

    /**
     * @var string
     *
     * @ORM\Column(name="relatedProductMode", type="text",  length=50, nullable = true)
     */
     private $relatedProductMode;

    /**
     * @var integer
     *
     * @ORM\Column(name="mobileProductColumn", type="smallint", nullable = true)
     */
     private $mobileProductColumn;

    /**
     * @var integer
     *
     * @ORM\Column(name="mobileFeatureColumn", type="smallint", nullable = true)
     */
     private $mobileFeatureColumn;

    /**
     * @var integer
     *
     * @ORM\Column(name="owlProductColumn", type="smallint", nullable = true)
     */
     private $owlProductColumn = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="gridProductColumn", type="smallint", nullable = true)
     */
     private $gridProductColumn = 4;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showSidebar", type="boolean")
     */
    private $showSidebar = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="cashOnDelivery", type="boolean")
     */
    private $cashOnDelivery = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mobileFilter", type="boolean")
     */
    private $mobileFilter = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="customTheme", type="boolean")
     */
    private $customTheme = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="orderDirectProcess", type="boolean")
     */
    private $orderDirectProcess = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="appHomeFeatureCategory", type="boolean")
     */
    private $appHomeFeatureCategory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="appHomeFeatureBrand", type="boolean")
     */
    private $appHomeFeatureBrand = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="appHomeFeatureDiscount", type="boolean")
     */
    private $appHomeFeatureDiscount = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="appHomeFeaturePromotion", type="boolean")
     */
    private $appHomeFeaturePromotion = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="productDetails", type="boolean")
     */
    private $productDetails = false;

    /**
     * @var string
     *
     * @ORM\Column(name="showBengal", type="string", length=50, nullable = true )
     */
    private $showBengal;

     /**
     * @var string
     *
     * @ORM\Column(name="productTheme", type="string", length=50, nullable = true )
     */
    private $productTheme;

    /**
     * @var boolean
     *
     * @ORM\Column(name="uploadFile", type="boolean")
     */
    private $uploadFile = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isOrderPoint", type="boolean")
     */
    private $isOrderPoint = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isInventoryStock", type="boolean")
     */
    private $isInventoryStock = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cartSearch", type="boolean")
     */
    private $cartSearch = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showMasterName", type="boolean")
     */
    private $showMasterName = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showBrand", type="boolean")
     */
    private $showBrand = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="searchCategory", type="boolean")
     */
    private $searchCategory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showCategory", type="boolean")
     */
    private $showCategory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="appHomeSlider", type="boolean")
     */
    private $appHomeSlider = false;



    /**
     * @var boolean
     *
     * @ORM\Column(name="isAdditionalItem", type="boolean")
     */
    private $isAdditionalItem = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sidebarBrand", type="boolean")
     */
    private $sidebarBrand = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sidebarDiscount", type="boolean")
     */
    private $sidebarDiscount = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sidebarPromotion", type="boolean")
     */
    private $sidebarPromotion = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sidebarTag", type="boolean")
     */
    private $sidebarTag = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sidebarCategory", type="boolean")
     */
    private $sidebarCategory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sidebarPrice", type="boolean")
     */
    private $sidebarPrice = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="footerConfig", type="boolean")
     */
    private $footerCategory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sidebarSize", type="boolean")
     */
    private $sidebarSize = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sidebarColor", type="boolean")
     */
    private $sidebarColor = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPreorder", type="boolean")
     */
    private $isPreorder = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cart", type="boolean")
     */
    private $cart = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSize", type="boolean")
     */
    private $isSize = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isColor", type="boolean")
     */
    private $isColor = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="webProduct", type="boolean")
     */
    private $webProduct = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="promotion", type="boolean")
     */
    private $promotion = false;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", nullable = true)
     */
    private $vat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="printBy", type="boolean",  nullable=true)
     */
    private $printBy = false;


    /**
     * @var array()
     *
     * @ORM\Column(name="printer", type="array", nullable=true)
     */
    private $printer;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=50,nullable = true)
     */
    private $address;


    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable=true)
     */
    private $vatRegNo;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginTop", type="smallint",  nullable=true)
     */
    private $printTopMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginBottom", type="smallint",  nullable=true)
     */
    private $printMarginBottom = 0;


     /**
     * @var smallint
     *
     * @ORM\Column(name="printLeftMargin", type="smallint",  nullable=true)
     */
    private $printLeftMargin = 0;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintHeader", type="boolean",  nullable=true)
     */
    private $isPrintHeader = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintFooter", type="boolean",  nullable=true)
     */
    private $isPrintFooter = true;


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

    /**
     * Set pickupLocation
     *
     * @param string $pickupLocation
     *
     * @return EcommerceConfig
     */
    public function setPickupLocation($pickupLocation)
    {
        $this->pickupLocation = $pickupLocation;

        return $this;
    }

    /**
     * Get pickupLocation
     *
     * @return string
     */
    public function getPickupLocation()
    {
        return $this->pickupLocation;
    }

    /**
     * Set isPreorder
     *
     * @param boolean $isPreorder
     *
     * @return EcommerceConfig
     */
    public function setIsPreorder($isPreorder)
    {
        $this->isPreorder = $isPreorder;

        return $this;
    }

    /**
     * Get isPreorder
     *
     * @return boolean
     */
    public function getIsPreorder()
    {
        return $this->isPreorder;
    }

    /**
     * Set cart
     *
     * @param boolean $cart
     *
     * @return EcommerceConfig
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart
     *
     * @return boolean
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set webProduct
     *
     * @param boolean $webProduct
     *
     * @return EcommerceConfig
     */
    public function setWebProduct($webProduct)
    {
        $this->webProduct = $webProduct;

        return $this;
    }

    /**
     * Get webProduct
     *
     * @return boolean
     */
    public function getWebProduct()
    {
        return $this->webProduct;
    }



    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param GlobalOption $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return PreOrder
     */
    public function getPreOrders()
    {
        return $this->preOrders;
    }

    /**
     * @param PreOrder $preOrders
     */
    public function setPreOrders($preOrders)
    {
        $this->preOrders = $preOrders;
    }

    /**
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }


    /**
     * @return boolean
     */
    public function isPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param boolean $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return int
     */
    public function getPerColumn()
    {
        return $this->perColumn;
    }

    /**
     * @param int $perColumn
     */
    public function setPerColumn($perColumn)
    {
        $this->perColumn = $perColumn;
    }

    /**
     * @return int
     */
    public function getOwlProductColumn()
    {
        return $this->owlProductColumn;
    }

    /**
     * @param int $owlProductColumn
     */
    public function setOwlProductColumn($owlProductColumn)
    {
        $this->owlProductColumn = $owlProductColumn;
    }


    /**
     * @return boolean
     */
    public function getIsColor()
    {
        return $this->isColor;
    }

    /**
     * @param boolean $isColor
     */
    public function setIsColor($isColor)
    {
        $this->isColor = $isColor;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
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
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param float $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return boolean
     */
    public function isVatEnable()
    {
        return $this->vatEnable;
    }

    /**
     * @param boolean $vatEnable
     */
    public function setVatEnable($vatEnable)
    {
        $this->vatEnable = $vatEnable;
    }

    /**
     * @return mixed
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param mixed $templates
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @return boolean
     */
    public function getShowMasterName()
    {
        return $this->showMasterName;
    }

    /**
     * @param boolean $showMasterName
     */
    public function setShowMasterName($showMasterName)
    {
        $this->showMasterName = $showMasterName;
    }

    /**
     * @return bool
     */
    public function getShowBrand()
    {
        return $this->showBrand;
    }

    /**
     * @param bool $showBrand
     */
    public function setShowBrand($showBrand)
    {
        $this->showBrand = $showBrand;
    }

    /**
     * @return bool
     */
    public function getSidebarBrand()
    {
        return $this->sidebarBrand;
    }

    /**
     * @param bool $sidebarBrand
     */
    public function setSidebarBrand($sidebarBrand)
    {
        $this->sidebarBrand = $sidebarBrand;
    }

    /**
     * @return bool
     */
    public function getSidebarCategory()
    {
        return $this->sidebarCategory;
    }

    /**
     * @param bool $sidebarCategory
     */
    public function setSidebarCategory($sidebarCategory)
    {
        $this->sidebarCategory = $sidebarCategory;
    }

    /**
     * @return bool
     */
    public function getSidebarPrice()
    {
        return $this->sidebarPrice;
    }

    /**
     * @param bool $sidebarPrice
     */
    public function setSidebarPrice($sidebarPrice)
    {
        $this->sidebarPrice = $sidebarPrice;
    }

    /**
     * @return bool
     */
    public function getSidebarSize()
    {
        return $this->sidebarSize;
    }

    /**
     * @param bool $sidebarSize
     */
    public function setSidebarSize($sidebarSize)
    {
        $this->sidebarSize = $sidebarSize;
    }

    /**
     * @return bool
     */
    public function getSidebarColor()
    {
        return $this->sidebarColor;
    }

    /**
     * @param bool $sidebarColor
     */
    public function setSidebarColor($sidebarColor)
    {
        $this->sidebarColor = $sidebarColor;
    }

    /**
     * @return bool
     */
    public function getIsSize()
    {
        return $this->isSize;
    }

    /**
     * @param bool $isSize
     */
    public function setIsSize($isSize)
    {
        $this->isSize = $isSize;
    }

    /**
     * @return bool
     */
    public function getShowSidebar()
    {
        return $this->showSidebar;
    }

    /**
     * @param bool $showSidebar
     */
    public function setShowSidebar($showSidebar)
    {
        $this->showSidebar = $showSidebar;
    }

    /**
     * @return string
     */
    public function getMenuType()
    {
        return $this->menuType;
    }

    /**
     * @param string $menuType
     * Mega
     * Dropdown
     * Sidebar
     */
    public function setMenuType($menuType)
    {
        $this->menuType = $menuType;
    }


    /**
     * @param string $vatRegNo
     */
    public function setVatRegNo($vatRegNo)
    {
        $this->vatRegNo = $vatRegNo;
    }

    /**
     * @return string
     */
    public function getVatRegNo()
    {
        return $this->vatRegNo;
    }


    /**
     * @return smallint
     */
    public function getPrintTopMargin()
    {
        return $this->printTopMargin;
    }

    /**
     * @param smallint $printTopMargin
     */
    public function setPrintTopMargin($printTopMargin)
    {
        $this->printTopMargin = $printTopMargin;
    }


    /**
     * @return smallint
     */
    public function getPrintMarginBottom()
    {
        return $this->printMarginBottom;
    }

    /**
     * @param smallint $printMarginBottom
     */
    public function setPrintMarginBottom($printMarginBottom)
    {
        $this->printMarginBottom = $printMarginBottom;
    }

    /**
     * @return boolean
     */
    public function isIsPrintHeader()
    {
        return $this->isPrintHeader;
    }

    /**
     * @param boolean $isPrintHeader
     */
    public function setIsPrintHeader($isPrintHeader)
    {
        $this->isPrintHeader = $isPrintHeader;
    }

    /**
     * @return boolean
     */
    public function isIsPrintFooter()
    {
        return $this->isPrintFooter;
    }

    /**
     * @param boolean $isPrintFooter
     */
    public function setIsPrintFooter($isPrintFooter)
    {
        $this->isPrintFooter = $isPrintFooter;
    }

    /**
     * @return smallint
     */
    public function getPrintLeftMargin()
    {
        return $this->printLeftMargin;
    }

    /**
     * @param smallint $printLeftMargin
     */
    public function setPrintLeftMargin($printLeftMargin)
    {
        $this->printLeftMargin = $printLeftMargin;
    }

    /**
     * @return boolean
     */
    public function getPrintBy()
    {
        return $this->printBy;
    }

    /**
     * @param boolean $printBy
     */
    public function setPrintBy($printBy)
    {
        $this->printBy = $printBy;
    }

    /**
     * @return boolean
     */
    public function getSidebarDiscount()
    {
        return $this->sidebarDiscount;
    }

    /**
     * @param boolean $sidebarDiscount
     */
    public function setSidebarDiscount($sidebarDiscount)
    {
        $this->sidebarDiscount = $sidebarDiscount;
    }

    /**
     * @return boolean
     */
    public function getSidebarPromotion()
    {
        return $this->sidebarPromotion;
    }

    /**
     * @param boolean $sidebarPromotion
     */
    public function setSidebarPromotion($sidebarPromotion)
    {
        $this->sidebarPromotion = $sidebarPromotion;
    }

    /**
     * @return boolean
     */
    public function getSidebarTag()
    {
        return $this->sidebarTag;
    }

    /**
     * @param boolean $sidebarTag
     */
    public function setSidebarTag($sidebarTag)
    {
        $this->sidebarTag = $sidebarTag;
    }

	/**
	 * @return mixed
	 */
	public function getCategoryGrouping() {
		return $this->categoryGrouping;
	}

	/**
	 * @return Category
	 */
	public function getCategories() {
		return $this->categories;
	}

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return bool
     */
    public function isAdditionalItem()
    {
        return $this->isAdditionalItem;
    }

    /**
     * @param bool $isAdditionalItem
     */
    public function setIsAdditionalItem($isAdditionalItem)
    {
        $this->isAdditionalItem = $isAdditionalItem;
    }

    /**
     * @return bool
     */
    public function isShowCategory()
    {
        return $this->showCategory;
    }

    /**
     * @param bool $showCategory
     */
    public function setShowCategory($showCategory)
    {
        $this->showCategory = $showCategory;
    }

    /**
     * Sets file.
     *
     * @param ItemBrand $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return ItemBrand
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

    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getGlobalOption()->getId().'/ecommerce';
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
        $filename = date('YmdHmi') . "_" . $this->getFile()->getClientOriginalName();
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
     * @return string
     */
    public function getTitleBar()
    {
        return $this->titleBar;
    }

    /**
     * @param string $titleBar
     */
    public function setTitleBar($titleBar)
    {
        $this->titleBar = $titleBar;
    }

    /**
     * @return string
     */
    public function getPaginationShow()
    {
        return $this->paginationShow;
    }

    /**
     * @param string $paginationShow
     */
    public function setPaginationShow($paginationShow)
    {
        $this->paginationShow = $paginationShow;
    }

    /**
     * @return bool
     */
    public function isSearchCategory()
    {
        return $this->searchCategory;
    }

    /**
     * @param bool $searchCategory
     */
    public function setSearchCategory($searchCategory)
    {
        $this->searchCategory = $searchCategory;
    }

    /**
     * @return bool
     */
    public function isUploadFile()
    {
        return $this->uploadFile;
    }

    /**
     * @param bool $uploadFile
     */
    public function setUploadFile($uploadFile)
    {
        $this->uploadFile = $uploadFile;
    }

    /**
     * @return bool
     */
    public function isCartSearch()
    {
        return $this->cartSearch;
    }

    /**
     * @param bool $cartSearch
     */
    public function setCartSearch($cartSearch)
    {
        $this->cartSearch = $cartSearch;
    }

    /**
     * @return bool
     */
    public function isFooterCategory()
    {
        return $this->footerCategory;
    }

    /**
     * @param bool $footerCategory
     */
    public function setFooterCategory($footerCategory)
    {
        $this->footerCategory = $footerCategory;
    }

    /**
     * @return string
     */
    public function getShowBengal()
    {
        return $this->showBengal;
    }

    /**
     * @param string $showBengal
     */
    public function setShowBengal($showBengal)
    {
        $this->showBengal = $showBengal;
    }

    /**
     * @return string
     */
    public function getCartProcess()
    {
        return $this->cartProcess;
    }

    /**
     * @param string $cartProcess
     */
    public function setCartProcess($cartProcess)
    {
        $this->cartProcess = $cartProcess;
    }



    /**
     * @return string
     */
    public function getProductTheme()
    {
        return $this->productTheme;
    }

    /**
     * @param string $productTheme
     */
    public function setProductTheme($productTheme)
    {
        $this->productTheme = $productTheme;
    }

    /**
     * @return int
     */
    public function getGridProductColumn()
    {
        return $this->gridProductColumn;
    }

    /**
     * @param int $gridProductColumn
     */
    public function setGridProductColumn($gridProductColumn)
    {
        $this->gridProductColumn = $gridProductColumn;
    }

    /**
     * @param array $printer
     */
    public function setPrinter($printer)
    {
        $this->printer = $printer;
    }

    /**
     * @return array
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @return bool
     */
    public function isProductDetails()
    {
        return $this->productDetails;
    }

    /**
     * @param bool $productDetails
     */
    public function setProductDetails($productDetails)
    {
        $this->productDetails = $productDetails;
    }

    /**
     * @return bool
     */
    public function isCustomTheme()
    {
        return $this->customTheme;
    }

    /**
     * @param bool $customTheme
     */
    public function setCustomTheme($customTheme)
    {
        $this->customTheme = $customTheme;
    }

    /**
     * @return string
     */
    public function getRelatedProduct()
    {
        return $this->relatedProduct;
    }

    /**
     * @param string $relatedProduct
     */
    public function setRelatedProduct($relatedProduct)
    {
        $this->relatedProduct = $relatedProduct;
    }

    /**
     * @return string
     */
    public function getRelatedProductMode()
    {
        return $this->relatedProductMode;
    }

    /**
     * @param string $relatedProductMode
     */
    public function setRelatedProductMode($relatedProductMode)
    {
        $this->relatedProductMode = $relatedProductMode;
    }

    /**
     * @return int
     */
    public function getMobileProductColumn()
    {
        return $this->mobileProductColumn;
    }

    /**
     * @param int $mobileProductColumn
     */
    public function setMobileProductColumn($mobileProductColumn)
    {
        $this->mobileProductColumn = $mobileProductColumn;
    }

    /**
     * @return int
     */
    public function getMobileFeatureColumn()
    {
        return $this->mobileFeatureColumn;
    }

    /**
     * @param int $mobileFeatureColumn
     */
    public function setMobileFeatureColumn($mobileFeatureColumn)
    {
        $this->mobileFeatureColumn = $mobileFeatureColumn;
    }

    /**
     * @return bool
     */
    public function isMobileFilter()
    {
        return $this->mobileFilter;
    }

    /**
     * @param bool $mobileFilter
     */
    public function setMobileFilter($mobileFilter)
    {
        $this->mobileFilter = $mobileFilter;
    }

    /**
     * @return bool
     */
    public function isCashOnDelivery()
    {
        return $this->cashOnDelivery;
    }

    /**
     * @param bool $cashOnDelivery
     */
    public function setCashOnDelivery($cashOnDelivery)
    {
        $this->cashOnDelivery = $cashOnDelivery;
    }

    /**
     * @return string
     */
    public function getProductMode()
    {
        return $this->productMode;
    }

    /**
     * @param string $productMode
     */
    public function setProductMode($productMode)
    {
        $this->productMode = $productMode;
    }

    /**
     * @return bool
     */
    public function isOrderPoint()
    {
        return $this->isOrderPoint;
    }

    /**
     * @param bool $isOrderPoint
     */
    public function setIsOrderPoint($isOrderPoint)
    {
        $this->isOrderPoint = $isOrderPoint;
    }

    /**
     * @return bool
     */
    public function isInventoryStock()
    {
        return $this->isInventoryStock;
    }

    /**
     * @param bool $isInventoryStock
     */
    public function setIsInventoryStock($isInventoryStock)
    {
        $this->isInventoryStock = $isInventoryStock;
    }

    /**
     * @return AppModule
     */
    public function getStockApplication()
    {
        return $this->stockApplication;
    }

    /**
     * @param AppModule $stockApplication
     */
    public function setStockApplication($stockApplication)
    {
        $this->stockApplication = $stockApplication;
    }

    /**
     * @return bool
     */
    public function isAppHomeFeatureCategory()
    {
        return $this->appHomeFeatureCategory;
    }

    /**
     * @param bool $appHomeFeatureCategory
     */
    public function setAppHomeFeatureCategory($appHomeFeatureCategory)
    {
        $this->appHomeFeatureCategory = $appHomeFeatureCategory;
    }

    /**
     * @return bool
     */
    public function isAppHomeFeatureBrand()
    {
        return $this->appHomeFeatureBrand;
    }

    /**
     * @param bool $appHomeFeatureBrand
     */
    public function setAppHomeFeatureBrand($appHomeFeatureBrand)
    {
        $this->appHomeFeatureBrand = $appHomeFeatureBrand;
    }

    /**
     * @return bool
     */
    public function isAppHomeFeatureDiscount()
    {
        return $this->appHomeFeatureDiscount;
    }

    /**
     * @param bool $appHomeFeatureDiscount
     */
    public function setAppHomeFeatureDiscount($appHomeFeatureDiscount)
    {
        $this->appHomeFeatureDiscount = $appHomeFeatureDiscount;
    }

    /**
     * @return bool
     */
    public function isAppHomeFeaturePromotion()
    {
        return $this->appHomeFeaturePromotion;
    }

    /**
     * @param bool $appHomeFeaturePromotion
     */
    public function setAppHomeFeaturePromotion($appHomeFeaturePromotion)
    {
        $this->appHomeFeaturePromotion = $appHomeFeaturePromotion;
    }

    /**
     * @return bool
     */
    public function isAppHomeSlider()
    {
        return $this->appHomeSlider;
    }

    /**
     * @param bool $appHomeSlider
     */
    public function setAppHomeSlider($appHomeSlider)
    {
        $this->appHomeSlider = $appHomeSlider;
    }

    /**
     * @return bool
     */
    public function isOrderDirectProcess()
    {
        return $this->orderDirectProcess;
    }

    /**
     * @param bool $orderDirectProcess
     */
    public function setOrderDirectProcess($orderDirectProcess)
    {
        $this->orderDirectProcess = $orderDirectProcess;
    }







}

