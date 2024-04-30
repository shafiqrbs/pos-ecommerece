<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * MegaMenu
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Entity\MegaMenuRepository")
 */
class MegaMenu
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\Syndicate", inversedBy="megaMenus")
     **/

    private $syndicates;


    /**
     * @ORM\ManyToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="megaMenu")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Collection", inversedBy="megaMenu")
     **/

    private $collections;


    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="megaMenu")
     **/

    private $globalOptions;




    /**
     * @var smallint
     *
     * @ORM\Column(name="sorting", type="smallint" , nullable=true)
     */
    private $sorting;



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
     * @return MegaMenu
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
     * @return MegaMenu
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
     * @return MegaMenu
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
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return mixed
     */
    public function getCollections()
    {
        return $this->collections;
    }

    /**
     * @param mixed $collections
     */
    public function setCollections($collections)
    {
        $this->collections = $collections;
    }

    /**
     * @return mixed
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param mixed $brands
     */
    public function setBrands($brands)
    {
        $this->brands = $brands;
    }

    /**
     * @return \Setting\Bundle\AppearanceBundle\Entity\smallint
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param \Setting\Bundle\AppearanceBundle\Entity\smallint $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return mixed
     */
    public function getSyndicates()
    {
        return $this->syndicates;
    }

    /**
     * @param mixed $syndicates
     */
    public function setSyndicates($syndicates)
    {
        $this->syndicates = $syndicates;
    }

    /**
     * @return mixed
     */
    public function getGlobalOptions()
    {
        return $this->globalOptions;
    }

    /**
     * @param mixed $globalOptions
     */
    public function setGlobalOptions($globalOptions)
    {
        $this->globalOptions = $globalOptions;
    }

    /**
     * @return mixed
     */
    public function getAdvertisments()
    {
        return $this->advertisments;
    }

    /**
     * @param mixed $advertisments
     */
    public function setAdvertisments($advertisments)
    {
        $this->advertisments = $advertisments;
    }
}
