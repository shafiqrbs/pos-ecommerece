<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu;
use Setting\Bundle\AppearanceBundle\Entity\Feature;
use Setting\Bundle\AppearanceBundle\Entity\FeatureBrand;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ItemBrand
 *
 * @ORM\Table("ecommerce_item_brand")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\ItemBrandRepository")
 */
class ItemBrand  implements CodeAwareEntity
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="brands" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $ecommerceConfig;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item", mappedBy="brand")
     */
    protected $items;


    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu", mappedBy="brands")
     */
    protected $ecommerceMenu;

     /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Discount", mappedBy="brand")
     */
    protected $discounts;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidget", mappedBy="brand")
     **/
    private $featureWidgets;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureBrand", mappedBy="brand")
     **/
    private $featureBrand;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Feature", mappedBy="brand")
     **/
    private $features;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable= true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="featureItem", nullable=true, type="integer")
     */
    private $featureItem = 12;


    /**
     * @var string
     *
     * @ORM\Column(name="nameBn", type="string", length=255, nullable=true)
     */
    private $nameBn;


     /**
     * @var string
     *
     * @ORM\Column(name="bgcolor", type="string", nullable = true)
     */
    private $bgcolor;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;


    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255)
     */
    private $slug;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable = true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="brandCode", type="string", length=50,nullable = true )
     */
    private $brandCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


	/**
     * @var boolean
     *
     * @ORM\Column(name="isFeature", type="boolean", nullable=true)
     */
    private $isFeature = false;



	/**
     * @var boolean
     *
     * @ORM\Column(name="feature", type="boolean", nullable=true)
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
     * Set code
     *
     * @param integer $code
     *
     * @return ItemBrand
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
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
     * @return mixed
     */
    public function getSTRPadCode()
    {
        $code = str_pad($this->getCode(),3, '0', STR_PAD_LEFT);
        return $code;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getBrandCode()
    {
        return $this->brandCode;
    }

    /**
     * @param string $brandCode
     */
    public function setBrandCode($brandCode)
    {
        $this->brandCode = $brandCode;
    }

    /**
     * @return EcommerceMenu
     */
    public function getEcommerceMenu()
    {
        return $this->ecommerceMenu;
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
     * @return FeatureBrand
     */
    public function getFeatureBrand()
    {
        return $this->featureBrand;
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
		return 'uploads/domain/'.$this->getEcommerceConfig()->getGlobalOption()->getId().'/brand/';
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
     * @return mixed
     */
    public function getisFeature()
    {
        return $this->isFeature;
    }

    /**
     * @param mixed $isFeature
     */
    public function setIsFeature($isFeature)
    {
        $this->isFeature = $isFeature;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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

