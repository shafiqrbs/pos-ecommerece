<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Order
 *
 * @ORM\Table("ems_order")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\OrderRepository")
 */
class Order
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\DeliveryLocation", inversedBy="orders")
     **/
    protected $location;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $globalOption;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\OrderPayment", mappedBy="order"  , cascade={"persist", "remove"} )
     * @ORM\OrderBy({"created" = "ASC"})
     **/
    private  $orderPayments;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\CourierService", inversedBy="orders" )
     **/
    private  $courier;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="order" )
     **/
    private  $inventorySales;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\OrderItem", mappedBy="order"  , cascade={"persist", "remove"} )
     **/
    private  $orderItems;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Coupon", inversedBy="orders" )
     **/
    private  $coupon;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\TimePeriod", inversedBy="orders" )
     */
    protected $timePeriod;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/

    private $processBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private $approvedBy;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="orders")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $customer;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank")
     **/
    private  $accountMobileBank;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank")
     **/
    private  $accountBank;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod")
     **/
    private  $transactionMethod;

    /**
     * @var float
     *
     * @ORM\Column(name="discountCalculation", type="float" , nullable=true)
     */
    private $discountCalculation = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="orderId", type="integer",  nullable=true)
     */
    private $orderId;

     /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50,  nullable=true)
     */
    private $process = 'created';

    /**
     * @var string
     *
     * @ORM\Column(name="delivery", type="string", length=50,  nullable=true)
     */
    private $delivery = 'delivery';


    /**
     * @var string
     *
     * @ORM\Column(name="payment_mode", type="string", length=50,  nullable=true)
     */
    private $paymentMode = 'Cash';


    /**
     * @var string
     *
     * @ORM\Column(name="paymentMobile", type="string", length=50,  nullable=true)
     */
    private $paymentMobile;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PaymentCard", inversedBy="sales" )
     **/
    private $paymentCard;


    /**
     * @var string
     *
     * @ORM\Column(name="cardNo", type="string", length=100, nullable=true)
     */
    private $cardNo;

    /**
     * @var string
     *
     * @ORM\Column(name="deliverySlot", type="string", length=100,  nullable=true)
     */
    private $deliverySlot;

    /**
     * @var \DateTime
     * @ORM\Column(name="deliveryDate", type="datetime" , nullable=true)
     */
    private $deliveryDate ;

    /**
     * @var string
     *
     * @ORM\Column(name="customerName", type="string", length=100,  nullable=true)
     */
    private $customerName;


    /**
     * @var string
     *
     * @ORM\Column(name="trackingNo", type="string", length=100,  nullable=true)
     */
    private $trackingNo;


    /**
     * @var string
     *
     * @ORM\Column(name="customerMobile", type="string", length=100,  nullable=true)
     */
    private $customerMobile;


    /**
     * @var string
     *
     * @ORM\Column(name="customerEmail", type="string", length=100,  nullable=true)
     */
    private $customerEmail;


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
     * @var text
     *
     * @ORM\Column(name="jsonOrder", type="text", nullable=true)
     */
    private $jsonOrder;

    /**
     * @var text
     *
     * @ORM\Column(name="jsonOrderItem", type="text", nullable=true)
     */
    private $jsonOrderItem;


    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255 , nullable = true)
     */
    private $invoice;


    /**
     * @var string
     *
     * @ORM\Column(name="mobileAccount", type="string", length=50 , nullable = true)
     */
    private $mobileAccount;


    /**
     * @var string
     *
     * @ORM\Column(name="accountType", type="string", length=255 , nullable = true)
     */
    private $accountType;


    /**
     * @var string
     *
     * @ORM\Column(name="transaction", type="string", length=255 , nullable = true)
     */
    private $transaction;


    /**
     * @var string
     *
     * @ORM\Column(name="accountName", type="string", length=50 , nullable = true)
     */
    private $accountName;


    /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=255 , nullable = true)
     */
    private $accountNo;


    /**
     * @var string
     *
     * @ORM\Column(name="bankBranch", type="string", length=255 , nullable = true)
     */
    private $bankBranch;

    /**
     * @var float
     *
     * @ORM\Column(name="apiId", type="integer", nullable = true)
     */
    private $apiId;

    /**
     * @var float
     *
     * @ORM\Column(name="orderPoint", type="integer", nullable = true)
     */
    private $orderPoint;

    /**
     * @var float
     *
     * @ORM\Column(name="shippingCharge", type="float", nullable = true)
     */
    private $shippingCharge;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", nullable = true)
     */
    private $vat;

    /**
     * @var float
     *
     * @ORM\Column(name="actualAmount", type="float", nullable = true)
     */
    private $actualAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable = true)
     */
    private $subTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable = true)
     */
    private $discount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable = true)
     */
    private $total;


    /**
     * @var float
     *
     * @ORM\Column(name="vatPercent", type="float", nullable = true)
     */
    private $vatPercent;

    /**
     * @var float
     *
     * @ORM\Column(name="receive", type="float" , nullable = true)
     */
    private $receive;

    /**
     * @var float
     *
     * @ORM\Column(name="due", type="float" , nullable = true)
     */
    private $due;

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
    private $discountAmount = 0;


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
     * @ORM\Column(name="paidAmount", type="float" , nullable = true)
     */
    private $paidAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="dueAmount", type="float" , nullable = true)
     */
    private $dueAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="couponAmount", type="float" , nullable = true)
     */
    private $couponAmount;


    /**
     * @var integer
     *
     * @ORM\Column(name="item", type="integer" , nullable = true)
     */
    private $item;


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
    private $status = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isArchive", type="boolean")
     */
    private $isArchive = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="specialDiscount", type="boolean")
     */
    private $specialDiscount = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDelete", type="boolean")
     */
    private $isDelete = 0;

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
     * Set invoice
     *
     * @param string $invoice
     *
     * @return Order
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }


    /**
     * Set process
     *
     * @param string $process
     *
     * @return Order
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
     * @return OrderItem
     */
    public function getOrderItems()
    {
        return $this->orderItems;
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
     * @return float
     */
    public function getDueAmount()
    {
        return $this->dueAmount;
    }

    /**
     * @param float $dueAmount
     */
    public function setDueAmount($dueAmount)
    {
        $this->dueAmount = $dueAmount;
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
     * @return int
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param int $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return float
     */
    public function getShippingCharge()
    {
        return $this->shippingCharge;
    }

    /**
     * @param float $shippingCharge
     */
    public function setShippingCharge($shippingCharge)
    {
        $this->shippingCharge = $shippingCharge;
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
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @return string
     */
    public function getMobileAccount()
    {
        return $this->mobileAccount;
    }

    /**
     * @param string $mobileAccount
     */
    public function setMobileAccount($mobileAccount)
    {
        $this->mobileAccount = $mobileAccount;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
    }

    /**
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param string $transaction
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return string
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     * @param string $accountName
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
    }

    /**
     * @return string
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * @param string $accountNo
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;
    }

    /**
     * @return string
     */
    public function getBankBranch()
    {
        return $this->bankBranch;
    }

    /**
     * @param string $bankBranch
     */
    public function setBankBranch($bankBranch)
    {
        $this->bankBranch = $bankBranch;
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
     * @return DeliveryLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param DeliveryLocation $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return boolean
     */
    public function isCashOnDelivery()
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
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @param Coupon $coupon
     */
    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * @return float
     */
    public function getCouponAmount()
    {
        return $this->couponAmount;
    }

    /**
     * @param float $couponAmount
     */
    public function setCouponAmount($couponAmount)
    {
        $this->couponAmount = $couponAmount;
    }

    public function getCheckRoleEcommerceOrder($role = NULL)
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
     * @return OrderPayment
     */
    public function getOrderPayments()
    {
        return $this->orderPayments;
    }

    /**
     * @return AccountMobileBank
     */
    public function getAccountMobileBank()
    {
        return $this->accountMobileBank;
    }

    /**
     * @param AccountMobileBank $accountMobileBank
     */
    public function setAccountMobileBank($accountMobileBank)
    {
        $this->accountMobileBank = $accountMobileBank;
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


    public function getUploadDir()
    {
        return 'uploads/domain/'.$this->getGlobalOption()->getId().'/order';
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
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getDeliverySlot()
    {
        return $this->deliverySlot;
    }

    /**
     * @param string $deliverySlot
     */
    public function setDeliverySlot($deliverySlot)
    {
        $this->deliverySlot = $deliverySlot;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @param string $customerName
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * @return string
     */
    public function getCustomerMobile()
    {
        return $this->customerMobile;
    }

    /**
     * @param string $customerMobile
     */
    public function setCustomerMobile($customerMobile)
    {
        $this->customerMobile = $customerMobile;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @param string $customerEmail
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
    }

    /**
     * @return TimePeriod
     */
    public function getTimePeriod()
    {
        return $this->timePeriod;
    }

    /**
     * @param TimePeriod $timePeriod
     */
    public function setTimePeriod($timePeriod)
    {
        $this->timePeriod = $timePeriod;
    }

    /**
     * @return string
     */
    public function getPaymentMobile()
    {
        return $this->paymentMobile;
    }

    /**
     * @param string $paymentMobile
     */
    public function setPaymentMobile($paymentMobile)
    {
        $this->paymentMobile = $paymentMobile;
    }

    /**
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param float $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return float
     */
    public function getReceive()
    {
        return $this->receive;
    }

    /**
     * @param float $receive
     */
    public function setReceive($receive)
    {
        $this->receive = $receive;
    }

    /**
     * @return float
     */
    public function getDue()
    {
        return $this->due;
    }

    /**
     * @param float $due
     */
    public function setDue($due)
    {
        $this->due = $due;
    }

    /**
     * @return float
     */
    public function getApiId()
    {
        return $this->apiId;
    }

    /**
     * @param float $apiId
     */
    public function setApiId($apiId)
    {
        $this->apiId = $apiId;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return mixed
     */
    public function getAccountBank()
    {
        return $this->accountBank;
    }

    /**
     * @param mixed $accountBank
     */
    public function setAccountBank($accountBank)
    {
        $this->accountBank = $accountBank;
    }

    /**
     * @return mixed
     */
    public function getTransactionMethod()
    {
        return $this->transactionMethod;
    }

    /**
     * @param mixed $transactionMethod
     */
    public function setTransactionMethod($transactionMethod)
    {
        $this->transactionMethod = $transactionMethod;
    }

    /**
     * @return CourierService
     */
    public function getCourier()
    {
        return $this->courier;
    }

    /**
     * @param CourierService $courier
     */
    public function setCourier($courier)
    {
        $this->courier = $courier;
    }

    /**
     * @return string
     */
    public function getTrackingNo()
    {
        return $this->trackingNo;
    }

    /**
     * @param string $trackingNo
     */
    public function setTrackingNo($trackingNo)
    {
        $this->trackingNo = $trackingNo;
    }

    /**
     * @return float
     */
    public function getVatPercent()
    {
        return $this->vatPercent;
    }

    /**
     * @param float $vatPercent
     */
    public function setVatPercent($vatPercent)
    {
        $this->vatPercent = $vatPercent;
    }

    /**
     * @return bool
     */
    public function isArchive()
    {
        return $this->isArchive;
    }

    /**
     * @param bool $isArchive
     */
    public function setIsArchive($isArchive)
    {
        $this->isArchive = $isArchive;
    }

    /**
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \Appstore\Bundle\DomainUserBundle\Entity\CustomerAddress
     */
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    /**
     * @param \Appstore\Bundle\DomainUserBundle\Entity\CustomerAddress $customerAddress
     */
    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;
    }

    /**
     * @return float
     */
    public function getOrderPoint()
    {
        return $this->orderPoint;
    }

    /**
     * @param float $orderPoint
     */
    public function setOrderPoint($orderPoint)
    {
        $this->orderPoint = $orderPoint;
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->isDelete;
    }

    /**
     * @param bool $isDelete
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;
    }

    /**
     * @return text
     */
    public function getJsonOrder()
    {
        return $this->jsonOrder;
    }

    /**
     * @param text $jsonOrder
     */
    public function setJsonOrder($jsonOrder)
    {
        $this->jsonOrder = $jsonOrder;
    }

    /**
     * @return text
     */
    public function getJsonOrderItem()
    {
        return $this->jsonOrderItem;
    }

    /**
     * @param text $jsonOrderItem
     */
    public function setJsonOrderItem($jsonOrderItem)
    {
        $this->jsonOrderItem = $jsonOrderItem;
    }

    /**
     * @return mixed
     */
    public function getInventorySales()
    {
        return $this->inventorySales;
    }

    /**
     * @return mixed
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @return string
     */
    public function getPaymentMode()
    {
        return $this->paymentMode;
    }

    /**
     * @param string $paymentMode
     */
    public function setPaymentMode($paymentMode)
    {
        $this->paymentMode = $paymentMode;
    }

    /**
     * @return float
     */
    public function getActualAmount()
    {
        return $this->actualAmount;
    }

    /**
     * @param float $actualAmount
     */
    public function setActualAmount($actualAmount)
    {
        $this->actualAmount = $actualAmount;
    }

    /**
     * @return mixed
     */
    public function getPaymentCard()
    {
        return $this->paymentCard;
    }

    /**
     * @param mixed $paymentCard
     */
    public function setPaymentCard($paymentCard)
    {
        $this->paymentCard = $paymentCard;
    }

    /**
     * @return string
     */
    public function getCardNo()
    {
        return $this->cardNo;
    }

    /**
     * @param string $cardNo
     */
    public function setCardNo($cardNo)
    {
        $this->cardNo = $cardNo;
    }

    /**
     * @return bool
     */
    public function isSpecialDiscount()
    {
        return $this->specialDiscount;
    }

    /**
     * @param bool $specialDiscount
     */
    public function setSpecialDiscount($specialDiscount)
    {
        $this->specialDiscount = $specialDiscount;
    }

    /**
     * @return float
     */
    public function getDiscountCalculation()
    {
        return $this->discountCalculation;
    }

    /**
     * @param float $discountCalculation
     */
    public function setDiscountCalculation($discountCalculation)
    {
        $this->discountCalculation = $discountCalculation;
    }





}

