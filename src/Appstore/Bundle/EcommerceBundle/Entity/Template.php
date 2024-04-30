<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Product\Bundle\ProductBundle\Entity\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Template
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\TemplateRepository")
 */
class Template
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="templates" )
     **/
    private  $ecommerceConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="template" )
     **/
    private  $category;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", inversedBy="templatePromotion" )
     **/
    private  $promotion;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", inversedBy="templateTag" )
     **/
    private  $tag;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Discount", inversedBy="template" )
     **/
    private  $discount;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255 , nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="targetTo", type="string", length=50 , nullable=true)
     */
    private $targetTo;

    /**
     * @var array
     *
     * @ORM\Column(name="featureFor", type="array",nullable=true)
     */
    private $featureFor;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text" , nullable=true)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="divWidth", type="integer",  nullable=true)
     */
    private $divWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="divHeight", type="integer", nullable=true)
     */
    private $divHeight;



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
     * @var smallint
     *
     * @ORM\Column(name="sorting", type="smallint" , nullable=true)
     */
    private $sorting;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isSlider", type="boolean")
     */
    private $isSlider = false;



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
     * @return Template
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
     * Set content
     *
     * @param string $content
     * @return Template
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Template
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
     * Sets file.
     *
     * @param Template $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Template
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
        return 'uploads/domain/'.$this->getEcommerceConfig()->getGlobalOption()->getId().'/content';
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
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return smallint
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param smallint $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
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
     * @return Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param Promotion $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return Promotion
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param Promotion $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
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
     * @return string
     */
    public function getTargetTo()
    {
        return $this->targetTo;
    }

    /**
     * @param string $targetTo
     * Promotion
     * Category
     * Tag
     * Discount
     */
    public function setTargetTo($targetTo)
    {
        $this->targetTo = $targetTo;
    }

    /**
     * @return EcommerceConfig
     */
    public function getEcommerceConfig()
    {
        return $this->ecommerceConfig;
    }

    /**
     * @param EcommerceConfig $ecommerceConfig
     */
    public function setEcommerceConfig($ecommerceConfig)
    {
        $this->ecommerceConfig = $ecommerceConfig;
    }

    /**
     * @return boolean
     */
    public function getIsSlider()
    {
        return $this->isSlider;
    }

    /**
     * @param boolean $isSlider
     */
    public function setIsSlider($isSlider)
    {
        $this->isSlider = $isSlider;
    }

    /**
     * @return string
     */
    public function getFeatureFor()
    {
        return $this->featureFor;
    }

    /**
     * @param string $featureFor
     * Tab
     * Carousel
     * Feature
     */
    public function setFeatureFor($featureFor)
    {
        $this->featureFor = $featureFor;
    }

  

    /**
     * @return mixed
     */
    public function getEhomeItems()
    {
        return $this->ehomeItems;
    }

    /**
     * @return int
     */
    public function getDivWidth()
    {
        return $this->divWidth;
    }

    /**
     * @param int $divWidth
     */
    public function setDivWidth($divWidth)
    {
        $this->divWidth = $divWidth;
    }

    /**
     * @return int
     */
    public function getDivHeight()
    {
        return $this->divHeight;
    }

    /**
     * @param int $divHeight
     */
    public function setDivHeight($divHeight)
    {
        $this->divHeight = $divHeight;
    }


}
