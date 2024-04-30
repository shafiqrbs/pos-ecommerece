<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu;
use Setting\Bundle\AppearanceBundle\Entity\Feature;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Discount
 *
 * @ORM\Table("ems_discount")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\DiscountRepository")
 */

class Discount
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="discounts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $ecommerceConfig;


    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemBrand", inversedBy="discounts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $brand;

    /**
     * @ORM\ManyToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="discounts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item", mappedBy="discount",cascade={"persist", "remove"} )

     */
    protected $items;


    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidget", mappedBy="discount" , cascade={"remove"})
     **/
    private  $featureWidgets;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Feature", mappedBy="discount" , cascade={"remove"})
     **/
    private  $features;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Template", mappedBy="discount"  , cascade={"persist", "remove"} )
     **/
    private  $template;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu", mappedBy="discounts" , cascade={"remove"})
     **/
    private  $ecommerceMenu;

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
     * @var string
     *
     * @ORM\Column(name="bgcolor", type="string", nullable = true)
     */
    private $bgcolor;


    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255)
     */
    private $slug;


    /**
     * @var string
     *
     * @ORM\Column(name="discountAmount", type="decimal", nullable = true)
     */
    private $discountAmount = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable = true)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="featureItem", nullable=true, type="integer")
     */
    private $featureItem = 12;

    /**
     * @var boolean
     *
     * @ORM\Column(name="discountSubItem", type="boolean")
     */
    private $discountSubItem = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status=true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="feature", type="boolean")
     */
    private $feature = false;


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
     * Set name
     *
     * @param string $name
     *
     * @return Discount
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
     * Set status
     *
     * @param boolean $status
     *
     * @return Discount
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSTRPadCode()
    {
        $code = str_pad($this->getCode(),2, '0', STR_PAD_LEFT);
        return $code;
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
     * @return mixed
     */
    public function getPurchaseVendorItems()
    {
        return $this->purchaseVendorItems;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getEcommerceConfig()
    {
        return $this->ecommerceConfig;
    }

    /**
     * @param mixed $ecommerceConfig
     */
    public function setEcommerceConfig($ecommerceConfig)
    {
        $this->ecommerceConfig = $ecommerceConfig;
    }


    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Template $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return FeatureWidget
     */
    public function getFeatureWidgets()
    {
        return $this->featureWidgets;
    }

    /**
     * @return Feature
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @return EcommerceMenu
     */
    public function getEcommerceMenu()
    {
        return $this->ecommerceMenu;
    }

    /**
     * Sets file.
     *
     * @param Page $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Page
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
            : $this->getUploadDir().'/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getEcommerceConfig()->getGlobalOption()->getId().'/discount';
    }

    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
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
        $this->path = $filename ;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @param string $discountAmount
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     * @return string
     */
    public function getNameDetails()
    {
        $type = $this->getType() == 'percentage' ? '%':'';
        return $this->getName().'('.$this->getDiscountAmount().')'.$type;
    }

    /**
     * @return mixed
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param mixed $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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
     * @return bool
     */
    public function isDiscountSubItem()
    {
        return $this->discountSubItem;
    }

    /**
     * @param bool $discountSubItem
     */
    public function setDiscountSubItem($discountSubItem)
    {
        $this->discountSubItem = $discountSubItem;
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
     * @return string
     */
    public function getBgcolor()
    {
        return $this->bgcolor;
    }

    /**
     * @param string $bgcolor
     */
    public function setBgcolor($bgcolor)
    {
        $this->bgcolor = $bgcolor;
    }

    /**
     * @return int
     */
    public function getFeatureItem()
    {
        return $this->featureItem;
    }

    /**
     * @param int $featureItem
     */
    public function setFeatureItem($featureItem)
    {
        $this->featureItem = $featureItem;
    }


}

