<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * conditionLedger
 *
 * @ORM\Table(name="account_condition_ledger")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountConditionLedgerRepository")
 */
class AccountConditionLedger
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption")
     **/
    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCondition", inversedBy="conditionLedgers" , cascade={"detach","merge"} )
     **/
    private  $condition;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer")
     **/
    private  $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod")
     **/
    private  $transactionMethod;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank")
     **/
    private  $accountBank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank")
     **/
    private  $accountMobileBank;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $approvedBy;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float",  nullable = true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="debit", type="float",  nullable = true)
     */
    private $debit;


    /**
     * @var float
     *
     * @ORM\Column(name="credit", type="float",  nullable = true)
     */
    private $credit;


    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="float",  nullable = true)
     */
    private $balance;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="text",  nullable = true)
     */
    private $remark;

 
    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable = true)
     */
    private $process;

 
    /**
     * @var string
     *
     * @ORM\Column(name="transactionType", type="string", length=25, nullable = true)
     */
    private $transactionType = 'Debit';

    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length=25, nullable = true)
     */
    private $mode;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;


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
     * Set amount
     *
     * @param float $amount
     *
     * @return conditionLedger
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }



    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return mixed
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param mixed $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
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
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     * Debit
     * Credit
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
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
     * @return AccountCash
     */
    public function getAccountCash()
    {
        return $this->accountCash;
    }


    /**
     * @return mixed
     */
    public function getAccountMobileBank()
    {
        return $this->accountMobileBank;
    }

    /**
     * @param mixed $accountMobileBank
     */
    public function setAccountMobileBank($accountMobileBank)
    {
        $this->accountMobileBank = $accountMobileBank;
    }


    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
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
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param mixed $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param float $credit
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
    }

    /**
     * @return float
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * @param float $debit
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;
    }

    /**
     * @return AccountCondition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param AccountCondition $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return BusinessInvoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param BusinessInvoice $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }


}

