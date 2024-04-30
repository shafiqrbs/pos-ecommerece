<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\LocationBundle\Entity\Location;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PreOrder
 *
 * @ORM\Table("ems_pre_order")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\PreOrderRepository")
 */
class PreOrder
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="preOrders")
     **/
    protected $location;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="preOrders")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $ecommerceConfig;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private $processBy;

     /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/

    private $approvedBy;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrderItem", mappedBy="preOrder"  , cascade={"persist", "remove"} )
     * @ORM\OrderBy({"created" = "ASC"})
     **/
    private  $preOrderItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrderPayment", mappedBy="preOrder"  , cascade={"persist", "remove"} )
     * @ORM\OrderBy({"created" = "ASC"})
     **/
    private  $preOrderPayments;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer",  nullable=true)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="item", type="integer",  nullable=true)
     */
    private $item;

    /**
     * @var float
     *
     * @ORM\Column(name="deliveryCharge", type="float",  nullable=true)
     */
    private $deliveryCharge;

    /**
     * @var float
     *
     * @ORM\Column(name="totalShippingCharge", type="float",  nullable=true)
     */
    private $totalShippingCharge;


    /**
     * @var float
     *
     * @ORM\Column(name="paidAmount", type="float",  nullable=true)
     */
    private $paidAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="prePaidAmount", type="float",  nullable=true)
     */
    private $prePaidAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="advanceAmount", type="float",  nullable=true)
     */
    private $advanceAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", nullable = true)
     */
    private $vat;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable = true)
     */
    private $totalAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="grandTotalAmount", type="float", nullable = true)
     */
    private $grandTotalAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="dueAmount", type="float" , nullable = true)
     */
    private $dueAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="returnAmount", type="float" , nullable = true)
     */
    private $returnAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="discountAmount", type="float" , nullable = true)
     */
    private $discountAmount;



    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255,  nullable=true)
     */
    private $process = 'created';

    /**
     * @var string
     *
     * @ORM\Column(name="delivery", type="string", length=255,  nullable=true)
     */
    private $delivery = 'pickup';

    /**
     * @var text
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var text
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cashOnDelivery", type="boolean")
     */
    private $cashOnDelivery = false;

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
     * @var \DateTime
      * @ORM\Column(name="deliveryDate", type="datetime" , nullable=true)
     */
    private $deliveryDate ;

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
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=50, nullable=true)
     */
    private $invoice;

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PreOrder
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set item
     *
     * @param integer $item
     *
     * @return PreOrder
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return integer
     */
    public function getItem()
    {
        return $this->item;
    }



    /**
     * Set advanceAmount
     *
     * @param float $advanceAmount
     *
     * @return PreOrder
     */
    public function setAdvanceAmount($advanceAmount)
    {
        $this->advanceAmount = $advanceAmount;

        return $this;
    }

    /**
     * Get advanceAmount
     *
     * @return float
     */
    public function getAdvanceAmount()
    {
        return $this->advanceAmount;
    }

    /**
     * Set dueAmount
     *
     * @param float $dueAmount
     *
     * @return PreOrder
     */
    public function setDueAmount($dueAmount)
    {
        $this->dueAmount = $dueAmount;

        return $this;
    }

    /**
     * Get dueAmount
     *
     * @return float
     */
    public function getDueAmount()
    {
        return $this->dueAmount;
    }


    /**
     * Set process
     *
     * @param string $process
     *
     * @return PreOrder
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return PreOrder
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
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getPreOrderItems()
    {
        return $this->preOrderItems;
    }

    /**
     * @return float
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * @param float $paidAmount
     */
    public function setPaidAmount($paidAmount)
    {
        $this->paidAmount = $paidAmount;
    }

    /**

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

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }


    /**
     * @return User
     */
    public function getProcessBy()
    {
        return $this->processBy;
    }

    /**
     * @param User $processBy
     */
    public function setProcessBy($processBy)
    {
        $this->processBy = $processBy;
    }

    /**
     * @return User
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param User $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
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
     * @return string
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param string $delivery
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return text
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param text $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getCheckRoleEcommercePreorder($role = NULL)
    {

       $roles = array(
            'ROLE_DOMAIN_INVENTORY_ECOMMERCE',
            'ROLE_DOMAIN_INVENTORY_ECOMMERCE_MANAGER',
            'ROLE_DOMAIN_INVENTORY_MANAGER',
            'ROLE_DOMAIN_INVENTORY_APPROVE',
            'ROLE_DOMAIN_MANAGER',
            'ROLE_DOMAIN'
        );

        if(in_array($role,$roles)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * @return mixed
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param mixed $deliveryDate
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
     * @return boolean
     */
    public function getCashOnDelivery()
    {
        return $this->cashOnDelivery;
    }

    /**
     * @param boolean $cashOnDelivery
     */
    public function setCashOnDelivery($cashOnDelivery)
    {
        $this->cashOnDelivery = $cashOnDelivery;
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
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return float
     */
    public function getReturnAmount()
    {
        return $this->returnAmount;
    }

    /**
     * @param float $returnAmount
     */
    public function setReturnAmount($returnAmount)
    {
        $this->returnAmount = $returnAmount;
    }

    /**
     * @return float
     */
    public function getGrandTotalAmount()
    {
        return $this->grandTotalAmount;
    }

    /**
     * @param float $grandTotalAmount
     */
    public function setGrandTotalAmount($grandTotalAmount)
    {
        $this->grandTotalAmount = $grandTotalAmount;
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @param float $discountAmount
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     * @return text
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return float
     */
    public function getDeliveryCharge()
    {
        return $this->deliveryCharge;
    }

    /**
     * @param float $deliveryCharge
     */
    public function setDeliveryCharge($deliveryCharge)
    {
        $this->deliveryCharge = $deliveryCharge;
    }

    /**
     * @return float
     */
    public function getTotalShippingCharge()
    {
        return $this->totalShippingCharge;
    }

    /**
     * @param float $totalShippingCharge
     */
    public function setTotalShippingCharge($totalShippingCharge)
    {
        $this->totalShippingCharge = $totalShippingCharge;
    }

    /**
     * @return float
     */
    public function getPrePaidAmount()
    {
        return $this->prePaidAmount;
    }

    /**
     * @param float $prePaidAmount
     */
    public function setPrePaidAmount($prePaidAmount)
    {
        $this->prePaidAmount = $prePaidAmount;
    }

    /**
     * @return PreOrderPayment
     */
    public function getPreOrderPayments()
    {
        return $this->preOrderPayments;
    }

    /**
     * Sets file.
     *
     * @param Feature $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Feature
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
        return 'uploads/domain/'.$this->getGlobalOption()->getId().'/pre-order';
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



}

