<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\PaymentType;

/**
 * OrderReturn
 *
 * @ORM\Table("ems_order_return")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\OrderReturnRepository")
 */
class OrderReturn
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="orders")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $ecommerceConfig;

   /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="orders")
    * @ORM\JoinColumn(onDelete="CASCADE")
    */
    protected $globalOption;


    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255 , nullable = true)
     */
    private $invoice;

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
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return mixed
     */
    public function getReturnItems()
    {
        return $this->returnItems;
    }

    /**
     * @param mixed $returnItems
     */
    public function setReturnItems($returnItems)
    {
        $this->returnItems = $returnItems;
    }

    /**
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param string $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }


}

