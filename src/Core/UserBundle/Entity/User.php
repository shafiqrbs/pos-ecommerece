<?php

namespace Core\UserBundle\Entity;


use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="fos_user")
 * @UniqueEntity(fields="username",message="User name already existing,Please try again.")
 * @UniqueEntity(fields="email",message="Email address already existing,Please try again.")
 * @ORM\Entity(repositoryClass="Core\UserBundle\Entity\Repository\UserRepository")
 */
class User extends BaseUser
{


	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $username;

	protected $role;

	protected $enabled = true;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="isDelete", type="boolean", nullable=true)
	 */
	private $isDelete = 0;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="domainOwner", type="smallint", nullable=true)
	 */
	private $domainOwner = 0;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="userGroup", type="string", length = 30, nullable=true)
	 */
	private $userGroup = "user";

	/**
	 * @var string
	 *
	 * @ORM\Column(name="appPassword", type="string", length = 30, nullable=true)
	 */
	private $appPassword = "@123456";

	/**
	 * @var array
	 *
	 * @ORM\Column(name="appRoles", type="array", nullable=true)
	 */
	private $appRoles;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="agent", type="boolean", nullable=true)
	 */
	private $agent = false;


	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $avatar;

	/**
	 * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
	 * @ORM\JoinTable(name="user_user_group",
	 *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
	 * )
	 */
	protected $groups;


	/**
	 * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption")
	 *  * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="globalOption_id", referencedColumnName="id")
	 * })
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 **/
	protected $globalOption;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", mappedBy="employee" )
     **/
    private  $accountHead;

	/**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll", mappedBy="employee" , cascade={"persist", "remove"})
     **/
    private  $employeePayroll;


	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll", mappedBy="approvedBy" )
     **/
    private  $payrollApproved;


	/**
	 * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", cascade={"persist", "remove"})
	 *
	 */
	protected $profile;

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


	public function isGranted($role)
	{
		$domain = $this->getRole();
		if('ROLE_SUPER_ADMIN' === $domain or 'ROLE_DOMAIN' === $domain) {
			return true;
		}elseif(in_array($role, $this->getRoles())){
			return true;
		}
		return false;
	}

    public function hasRoles($role)
    {
        $array = array_intersect($role, $this->getRoles());
        if(!empty($array)){
            return true;
        }
        return false;
    }

	/**
	 * Set username;
	 *
	 * @param string $username
	 * @return User
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	public function getUserFullName(){
        if($this->profile){
            return $this->profile->getName();
        }
        return false;
	}

	public function userDoctor(){

		if(!empty($this->profile->getDesignation())){
			$designation = $this->profile->getDesignation()->getName();
		}else{
			$designation ='';
		}

		return $this->profile->getName().' ('.$designation.')';
	}

    public function userMarketingExecutive(){

        if(!empty($this->profile->getDesignation())){
            $designation = $this->profile->getDesignation()->getName();
        }else{
            $designation ='';
        }
        return $this->profile->getName().' ('.$designation.')';
    }

	public function toArray($collection)
	{
		$this->setRoles($collection->toArray());
	}

	public function setRole($role)
	{
		$this->getRoles();
		$this->addRole($role);

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRole()
	{
		$role = $this->getRoles();
		return $role[0];

	}


	/**
	 * @param Profile $profile
	 */
	public function setProfile($profile)
	{
		$profile->setUser($this);
		$this->profile = $profile;
	}

	/**
	 * @return Profile
	 */
	public function getProfile()
	{
		return $this->profile;
	}

	/**
	 * get avatar image file name
	 *
	 * @return string
	 */
	public function getAvatar()
	{
		return $this->avatar;
	}

	/**
	 * set avatar image file name
	 */
	public function setAvatar($avatar)
	{
		$this->avatar = $avatar;
	}

	public function isSuperAdmin()
	{
		$groups = $this->getGroups();
		foreach ($groups as $group) {
			if ($group->hasRole('ROLE_SUPER_ADMIN')) {
				return true;
			}
		}
		return false;
	}

	public function isRoleAdmin()
	{
		$groups = $this->getGroups();
		foreach ($groups as $group) {
			if ($group->hasRole('ROLE_ADMIN')) {
				return true;
			}
		}
		return false;
	}


	/**
	 * @param mixed $education
	 */
	public function setEducation($education)
	{
		$education->setUser($this);
		$this->education = $education;
	}

	/**
	 * @return mixed
	 */
	public function getEducation()
	{
		return $this->education;
	}

	/**
	 * @return mixed
	 */
	public function getPages()
	{
		return $this->pages;
	}


	/**
	 * @param mixed $siteSetting
	 */
	public function setSiteSetting($siteSetting)
	{
		$siteSetting->setUser($this);
		$this->siteSetting = $siteSetting;
	}

	/**
	 * @return mixed
	 */
	public function getSiteSetting()
	{
		return $this->siteSetting;
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
	 * @return mixed
	 */
	public function getHomePage()
	{
		return $this->homePage;
	}

	/**
	 * @return mixed
	 */
	public function getContactPage()
	{
		return $this->contactPage;
	}

	/**
	 * @return mixed
	 */
	public function getSyndicateContents()
	{
		return $this->syndicateContents;
	}

	/**
	 * @param mixed $vendor
	 */
	public function setVendor($vendor)
	{
		$this->vendor = $vendor;
	}

	/**
	 * @return mixed
	 */
	public function getCategoryGrouping()
	{
		return $this->categoryGrouping;
	}


	/**
	 * @return mixed
	 */
	public function getSalesUser()
	{
		return $this->salesUser;
	}

	/**
	 * @return mixed
	 */
	public function getSales()
	{
		return $this->sales;
	}

	/**
	 * @return mixed
	 */
	public function getPurchaseReturn()
	{
		return $this->purchaseReturn;
	}

	/**
	 * @return mixed
	 */
	public function getPurchasesReturnApprovedBy()
	{
		return $this->purchasesReturnApprovedBy;
	}


	/**
	 * @return boolean
	 */
	public function getIsDelete()
	{
		return $this->isDelete;
	}

	/**
	 * @param boolean $isDelete
	 */
	public function setIsDelete($isDelete)
	{
		$this->isDelete = $isDelete;
	}

	/**
	 * @return mixed
	 */
	public function getSalesReturn()
	{
		return $this->salesReturn;
	}

	/**
	 * @return mixed
	 */
	public function getPattyCash()
	{
		return $this->pattyCash;
	}

	/**
	 * @return mixed
	 */
	public function getPettyCashApprove()
	{
		return $this->pettyCashApprove;
	}

	/**
	 * @return mixed
	 */
	public function getExpenditure()
	{
		return $this->expenditure;
	}

	/**
	 * @return mixed
	 */
	public function getExpenditureToUser()
	{
		return $this->expenditureToUser;
	}

	/**
	 * @return mixed
	 */
	public function getExpenditureApprove()
	{
		return $this->expenditureApprove;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentSalaries()
	{
		return $this->paymentSalaries;
	}

	/**
	 * @return mixed
	 */
	public function getSalesApprovedBy()
	{
		return $this->salesApprovedBy;
	}

	/**
	 * @return mixed
	 */
	public function getInvoiceSmsEmail()
	{
		return $this->invoiceSmsEmail;
	}

	/**
	 * @return mixed
	 */
	public function getInvoiceSmsEmailReceivedBy()
	{
		return $this->invoiceSmsEmailReceivedBy;
	}

	/**
	 * @return mixed
	 */
	public function getSalesImport()
	{
		return $this->salesImport;
	}

	/**
	 * @return StockItem
	 */
	public function getStockItems()
	{
		return $this->stockItems;
	}

	/**
	 * @return Order
	 */
	public function getOrders()
	{
		return $this->orders;
	}

	/**
	 * @return PreOrder
	 */
	public function getPreOrders()
	{
		return $this->preOrders;
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


	public function getCheckRoleGlobal($existRole = NULL)
	{
		$result = array_intersect($existRole, $this->getRoles());
		if(empty($result)){
			return false;
		}else{
			return true;
		}

	}


    public function getCheckExistRole($existRole = NULL)
    {
        $result = in_array($existRole, $this->getRoles());
        if(empty($result)){
            return false;
        }else{
            return true;
        }

    }
	/**
	 * @return PreOrder
	 */
	public function getPreOrderProcess()
	{
		return $this->preOrderProcess;
	}

	/**
	 * @return PreOrder
	 */
	public function getPreOrderApproved()
	{
		return $this->preOrderApproved;
	}

	/**
	 * @return Damage
	 */
	public function getDamageApprovedBy()
	{
		return $this->damageApprovedBy;
	}

	/**
	 * @return Damage
	 */
	public function getDamage()
	{
		return $this->damage;
	}

	/**
	 * @return Order
	 */
	public function getOrderProcess()
	{
		return $this->orderProcess;
	}

	/**
	 * @return Order
	 */
	public function getOrderApproved()
	{
		return $this->orderApproved;
	}



	/**
	 * @return Branches
	 */
	public function getBranches()
	{
		return $this->branches;
	}

	/**
	 * @return BranchInvoice
	 */
	public function getBranchInvoice()
	{
		return $this->branchInvoice;
	}

	/**
	 * @return BranchInvoice
	 */
	public function getBranchInvoiceApprovedBy()
	{
		return $this->branchInvoiceApprovedBy;
	}

	/**
	 * @return ExcelImporter
	 */
	public function getExcelImporters()
	{
		return $this->excelImporters;
	}

	/**
	 * @return Delivery
	 */
	public function getDelivery()
	{
		return $this->delivery;
	}

	/**
	 * @return Delivery
	 */
	public function getDeliveryApprovedBy()
	{
		return $this->deliveryApprovedBy;
	}

	/**
	 * @return DeliveryReturn
	 */
	public function getDeliveryReturn()
	{
		return $this->deliveryReturn;
	}

	/**
	 * @return DeliveryReturn
	 */
	public function getDeliveryReturnApprovedBy()
	{
		return $this->deliveryReturnApprovedBy;
	}

	/**
	 * @return GlobalOption
	 */
	public function getGlobalOptionAgents()
	{
		return $this->globalOptionAgents;
	}

	/**
	 * @return mixed
	 */
	public function getAgent()
	{
		return $this->agent;
	}

	/**
	 * @param mixed $agent
	 */
	public function setAgent($agent)
	{
		$this->agent = $agent;
	}

	/**
	 * @return Particular
	 */
	public function getParticularOperator()
	{
		return $this->particularOperator;
	}

	/**
	 * @return InvoiceParticular
	 */
	public function getHmsInvoiceParticularCollected()
	{
		return $this->hmsInvoiceParticularCollected;
	}

	/**
	 * @return DailyAttendance
	 */
	public function getUserAttendance()
	{
		return $this->userAttendance;
	}


	/**
	 * @return DailyAttendance
	 */
	public function getUserAttendanceMonth($year,$month)
	{
		$attendances = $this->getUserAttendance();

		/* @var DailyAttendance $attendance */

		$presentDays = array();
		foreach ($attendances as $attendance){
			if($attendance->getYear() == $year and $attendance->getMonth() == $month ){
				$presentDays[] = $attendance->getPresentDay();
			}
		}
		return $presentDays;
	}

	/**
	 * @return HrAttendanceMonth
	 */
	public function getMonthlyPresentDay($year,$month)
	{
		$attendances = $this->getUserAttendance();

		/* @var HrAttendanceMonth $attendance */

		$presentDays = array();
		foreach ($attendances as $attendance){
			if($attendance->getYear() == $year and $attendance->getMonth() == $month ){
				$presentDays[] = $attendance->getPresentDay();
			}
		}
		return count($presentDays);
	}

	/**
	 * @return OrderPayment
	 */
	public function getOrderPayments()
	{
		return $this->orderPayments;
	}

	/**
	 * @return PreOrderPayment
	 */
	public function getPreOrderPayments()
	{
		return $this->preOrderPayments;
	}

	/**
	 * @return DmsParticular
	 */
	public function getDmsParticularDoctor()
	{
		return $this->dmsParticularDoctor;
	}

	/**
	 * @return HmsInvoiceTemporaryParticular
	 */
	public function getHmsInvoiceTemporaryParticulars()
	{
		return $this->hmsInvoiceTemporaryParticulars;
	}

	/**
	 * @return MedicineReverse
	 */
	public function getMedicineReverse()
	{
		return $this->medicineReverse;
	}

	/**
	 * @return DpsParticular
	 */
	public function getDpsParticularOperator()
	{
		return $this->dpsParticularOperator;
	}

	/**
	 * @return MedicinePurchase
	 */
	public function getMedicinePurchasesBy()
	{
		return $this->medicinePurchasesBy;
	}

	/**
	 * @return MedicineSalesTemporary
	 */
	public function getMedicineSalesTemporary()
	{
		return $this->medicineSalesTemporary;
	}

	/**
	 * @return int
	 */
	public function getDomainOwner()
	{
		return $this->domainOwner;
	}

	/**
	 * @param int $domainOwner
	 */
	public function setDomainOwner($domainOwner)
	{
		$this->domainOwner = $domainOwner;
	}

	/**
	 * @return DomainUser
	 */
	public function getDomainUser()
	{
		return $this->domainUser;
	}

	/**
	 * @return CustomerInvoice
	 */
	public function getCustomerInvoice() {
		return $this->customerInvoice;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(){
		return $this->enabled;
	}

	/**
	 * @return HotelTemporaryInvoice
	 */
	public function getHotelTemporary() {
		return $this->hotelTemporary;
	}

	/**
	 * @return AccountCash
	 */
	public function getAccountCashes() {
		return $this->accountCashes;
	}

    /**
     * @return HmsInvoiceReturn
     */
    public function getHmsInvoiceReturnCreatedBy()
    {
        return $this->hmsInvoiceReturnCreatedBy;
    }

    /**
     * @return HmsInvoiceReturn
     */
    public function getHmsInvoiceReturnApprovedBy()
    {
        return $this->hmsInvoiceReturnApprovedBy;
    }

    /**
     * @return RestaurantTemporary
     */
    public function getRestaurantTemps()
    {
        return $this->restaurantTemps;
    }

    /**
     * @return AccountSalesAdjustment
     */
    public function getSalesAdjustment()
    {
        return $this->salesAdjustment;
    }

    /**
     * @return AccountSalesAdjustment
     */
    public function getSalesAdjustmentApprove()
    {
        return $this->salesAdjustmentApprove;
    }

    /**
     * @return string
     */
    public function getUserGroup()
    {
        return $this->userGroup;
    }

    /**
     * @param string $userGroup
     */
    public function setUserGroup($userGroup)
    {
        $this->userGroup = $userGroup;
    }

    /**
     * @return AccountHead
     */
    public function getAccountHead()
    {
        return $this->accountHead;
    }


    /**
     * @return EmployeePayroll
     */
    public function getPayrollApproved()
    {
        return $this->payrollApproved;
    }

    /**
     * @param  EmployeePayroll $employeePayroll
     */
    public function setEmployeePayroll($employeePayroll)
    {
        $employeePayroll->setEmployee($this);
        $this->employeePayroll = $employeePayroll;
    }

     /**
     * @return EmployeePayroll
     */
    public function getEmployeePayroll()
    {
        return $this->employeePayroll;
    }

    /**
     * @return array
     */
    public function getAppRoles()
    {
        return $this->appRoles;
    }

    /**
     * @param array $appRoles
     */
    public function setAppRoles($appRoles)
    {
        $this->appRoles = $appRoles;
    }

    /**
     * @return string
     */
    public function getAppPassword()
    {
        return $this->appPassword;
    }

    /**
     * @param string $appPassword
     */
    public function setAppPassword($appPassword)
    {
        $this->appPassword = $appPassword;
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



}