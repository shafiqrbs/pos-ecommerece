<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * PreOrderPayment
 *
 * @ORM\Table("ems_preorder_payment")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\PreOrderPaymentRepository")
 */
class PreOrderPayment
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", inversedBy="preOrderPayments")
     **/
    private  $preOrder;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $createdBy;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash")
     **/
    private  $accountCash;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank")
     **/
    private  $accountBank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank")
     **/
    private  $accountMobileBank;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod")
     **/
    private  $transactionMethod;

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
     * @ORM\Column(name="amount", type="float",  nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="transactionType", type="string", length=20 , nullable = true)
     */
    private $transactionType;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint" ,length=1 ,nullable = true)
     */
    private $status = 0;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @return PreOrder
     */
    public function getPreOrder()
    {
        return $this->preOrder;
    }

    /**
     * @param PreOrder $preOrder
     */
    public function setPreOrder($preOrder)
    {
        $this->preOrder = $preOrder;
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
     * @return TransactionMethod
     */
    public function getTransactionMethod()
    {
        return $this->transactionMethod;
    }

    /**
     * @param TransactionMethod $transactionMethod
     */
    public function setTransactionMethod($transactionMethod)
    {
        $this->transactionMethod = $transactionMethod;
    }

    /**
     * @return mixed
     */
    public function getAccountCash()
    {
        return $this->accountCash;
    }

    /**
     * @param mixed $accountCash
     */
    public function setAccountCash($accountCash)
    {
        $this->accountCash = $accountCash;
    }

    /**
     * @return AccountBank
     */
    public function getAccountBank()
    {
        return $this->accountBank;
    }

    /**
     * @param AccountBank $accountBank
     */
    public function setAccountBank($accountBank)
    {
        $this->accountBank = $accountBank;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}

