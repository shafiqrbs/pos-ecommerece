<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ItemMetaAttribute
 *
 * @ORM\Table("ecommerce_item_key_value")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\ItemKeyValueRepository")
 */
class ItemKeyValue
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item", inversedBy="itemKeyValues" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $item;


    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", length=255, nullable = true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="text", nullable = true)
     */
    private $metaValue;

    /**
     * @var Integer
     *
     * @ORM\Column(name="sorting", type="smallint", length=2, nullable = true)
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
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param string $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return string
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param string $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

   
    /**
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
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

