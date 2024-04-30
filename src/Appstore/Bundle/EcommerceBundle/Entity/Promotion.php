<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu;
use Setting\Bundle\AppearanceBundle\Entity\Feature;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Promotion
 *
 * @ORM\Table("ems_promotion")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\PromotionRepository")
 */

class Promotion
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="promotions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $ecommerceConfig;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Template", mappedBy="promotion"  , cascade={"persist", "remove"} )
     **/
    private  $templatePromotion;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Template", mappedBy="tag"  , cascade={"persist", "remove"} )
     **/
    private  $templateTag;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidget", mappedBy="promotion" , cascade={"remove"})
     **/
    private  $featureWidgetPromotions;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidget", mappedBy="tag" , cascade={"remove"})
     **/
    private  $featureWidgetTags;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Feature", mappedBy="promotion")
     **/
    private  $featurePromotions;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Feature", mappedBy="tag")
     **/
    private  $featureTags;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu", mappedBy="promotions")
     **/
    private  $ecommerceMenuPromotion;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu", mappedBy="tags")
     **/
    private  $ecommerceMenuTag;


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
     * @var array
     *
     * @ORM\Column(name="type", type="array", nullable=true)
     */
    private $type;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255)
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable = true)
     */
    private $code;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status=true;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
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
     * @var boolean
     *
     * @ORM\Column(name="feature", type="boolean", nullable=true)
     */
    private $feature = false;


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
     * @return Promotion
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
     * @return Promotion
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
     * @return Promotion
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
     * @return PurchaseVendorItem
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
     * @return boolean
     */
    public function isFeature()
    {
        return $this->feature;
    }

    /**
     * @param boolean $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
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
        return 'uploads/domain/'.$this->getEcommerceConfig()->getGlobalOption()->getId().'/promotion';
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
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getItemTags()
    {
        return $this->itemTags;
    }


    /**
     * @return Template
     */
    public function getTemplateTag()
    {
        return $this->templateTag;
    }

    /**
     * @param Template $templateTag
     */
    public function setTemplateTag($templateTag)
    {
        $this->templateTag = $templateTag;
    }

    /**
     * @return Template
     */
    public function getTemplatePromotion()
    {
        return $this->templatePromotion;
    }

    /**
     * @param Template $templatePromotion
     */
    public function setTemplatePromotion($templatePromotion)
    {
        $this->templatePromotion = $templatePromotion;
    }

    /**
     * @return FeatureWidget
     */
    public function getFeatureWidgetPromotions()
    {
        return $this->featureWidgetPromotions;
    }

    /**
     * @return FeatureWidget
     */
    public function getFeatureWidgetTags()
    {
        return $this->featureWidgetTags;
    }

    /**
     * @return Feature
     */
    public function getFeaturePromotions()
    {
        return $this->featurePromotions;
    }

    /**
     * @return Feature
     */
    public function getFeatureTags()
    {
        return $this->featureTags;
    }


    /**
     * @return EcommerceMenu
     */
    public function getEcommerceMenuPromotion()
    {
        return $this->ecommerceMenuPromotion;
    }

    /**
     * @return EcommerceMenu
     */
    public function getEcommerceMenuTag()
    {
        return $this->ecommerceMenuTag;
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
     * @return mixed
     */
    public function getItemPromotions()
    {
        return $this->itemPromotions;
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




}

