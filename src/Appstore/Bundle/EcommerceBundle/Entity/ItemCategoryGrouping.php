<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ItemTypeGrouping
 *
 * @ORM\Table("ecommerce_category_grouping")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\ItemCategoryGroupingRepository")
 */
class ItemCategoryGrouping
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
     * @ORM\ManyToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="parentGroup" , cascade={"detach","merge"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"name" = "ASC"})
     **/

    protected $categories;


    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="categoryGrouping")
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private $ecommerceConfig;


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
}

