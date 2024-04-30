<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ItemMetaAttribute
 *
 * @ORM\Table("ecommerce_item_meta_attribute")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\ItemMeatAttributeRepository")
 */
class ItemMetaAttribute
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item", inversedBy="itemMetaAttributes" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $item;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemAttribute", inversedBy="itemMetaAttributes" )
     **/
    private  $itemAttribute;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


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
    public function getItemAttribute()
    {
        return $this->itemAttribute;
    }

    /**
     * @param mixed $itemAttribute
     */
    public function setItemAttribute($itemAttribute)
    {
        $this->itemAttribute = $itemAttribute;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }



	/**
	 * @return Item
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * @param Item $item
	 */
	public function setItem( $item ) {
		$this->item = $item;
	}
}

