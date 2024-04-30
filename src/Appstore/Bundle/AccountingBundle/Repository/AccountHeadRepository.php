<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountCondition;
use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Entity\AccountLoan;
use Appstore\Bundle\AccountingBundle\Entity\AccountLoanUser;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountHeadRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountHeadRepository extends EntityRepository
{



    public function getBalanceSheetAccount($global)
    {
        $accountHead = $this->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = array();
        /* @var $child AccountHead */
        foreach ($accountHead as $row){
            $childs = $this->getChildrenAccount($row->getId());
            if($childs){
                foreach ($childs as $child) {
                    $heads[$row->getId()][] = $child;
                    $subs = $this->getChildrenAccount($child['id'],$global);
                    if ($subs) {
                        foreach ($subs as $sub) {
                            $heads[$child['id']][] = $sub;
                        }
                    }
                }
            }
        }
        return $heads;
    }

    public function getChildrenTransactionAccount($parent = '', $option = '')
    {
        $query = $this->createQueryBuilder('e');
        $query->leftJoin('e.parent','p');
        $query->select('e.id as id');
        $query->addSelect('e.name as name');
        $query->addSelect('e.toIncrease as toIncrease');
        $query->addSelect('e.code as code');
        $query->addSelect('p.name as parentName');
        $query->where("e.status =1");
        if(!empty($parent)) {
            $query->andWhere("e.parent =:parent");
            $query->setParameter('parent', $parent);
        }
        if(!empty($option)) {
            $query->andWhere("e.globalOption =:option");
            $query->setParameter('option', $option);
        }
        $query->orderBy('e.name', 'ASC');
        return $query->getQuery()->getArrayResult();

    }

    public function getAllChildrenAccount($global)
    {
        $accountHead = $this->findBy(array('isParent' => 1,'status' => 1),array('name'=>'ASC'));
        $heads = array();
        /* @var $child AccountHead */
        foreach ($accountHead as $row){
            $childs = $this->getChildrenAccount($row->getId());
            if($childs){
                foreach ($childs as $child) {
                    $heads[$row->getId()][] = $child;
                    $subs = $this->getChildrenAccount($child['id'],$global);
                    if ($subs) {
                        foreach ($subs as $sub) {
                            $heads[$child['id']][] = $sub;
                        }
                    }
                }
            }
        }
        return $heads;
    }

    public function getChildrenAccount($parent = '', $option = '')
    {
        $query = $this->createQueryBuilder('e');
        $query->leftJoin('e.parent','p');
        $query->select('e.id as id');
        $query->addSelect('e.name as name');
        $query->addSelect('e.toIncrease as toIncrease');
        $query->addSelect('e.code as code');
        $query->addSelect('p.name as parentName');
        $query->where("e.status =1");
        if(!empty($parent)) {
            $query->andWhere("e.parent =:parent");
            $query->setParameter('parent', $parent);
        }
        if(!empty($option)) {
            $query->andWhere("e.globalOption =:option");
            $query->setParameter('option', $option);
        }
        $query->orderBy('e.name', 'ASC');
        return $query->getQuery()->getArrayResult();

    }




    public function getChildrenAccountHead($parent = '')
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.id as id');
        $query->addSelect('e.name as name');
        $query->addSelect('e.toIncrease as toIncrease');
        if(!empty($parent)) {
            $query->where("e.parent IN (:parent)");
            $query->setParameter('parent', $parent);
        }
        $query->orderBy('e.name', 'ASC');
        return $query->getQuery()->getResult();

    }

    public function getAccountHeadTrees(){

        $ret = array();
        $parent = array(23,37,9);
        $query = $this->createQueryBuilder('e');
        $query->select('e');
        $query->where("e.parent IN (:parent)");
        $query->setParameter('parent', $parent);
        $query->orderBy('e.name', 'ASC');
        $accountHeads =  $query->getQuery()->getResult();
        foreach( $accountHeads as $cat ){
            if( !$cat->getParent() ){
                continue;
            }
            $key = $cat->getParent()->getName();
            if(!array_key_exists($key, $ret) ){
                $ret[ $cat->getParent()->getName() ] = array();
            }
            $ret[ $cat->getParent()->getName() ][ $cat->getId() ] = $cat;
        }
        return $ret;


    }

	public function getExpenseAccountHead(){

		$ret = array();
		$parent = array(23,37);
		$query = $this->createQueryBuilder('e');
		$query->select('e');
		$query->where("e.id IN (:parent)");
		$query->setParameter('parent', $parent);
		$query->orderBy('e.name', 'ASC');
		$accountHeads =  $query->getQuery()->getResult();
     	return $accountHeads;

		//\Doctrine\Common\Util\Debug::dump($ret);
		//exit;

	}

	public function insertBankAccount(AccountBank $entity)
    {

        /* @var $exist AccountHead */
        $exist = $this->findOneBy(array('accountBank' => $entity));
        if ($exist) {
            $exist->setName($entity->getName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'bank-account'));
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($entity->getName());
            $head->setSource('bank');
            $head->setParent($parent);
            $head->setAccountBank($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }

    }

    public function insertMobileBankAccount(AccountMobileBank $entity)
    {

        /* @var $exist AccountHead */
        $exist = $this->findOneBy(array('accountMobileBank' => $entity));
        if ($exist) {
            $exist->setName($entity->getName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'mobile-account'));
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($entity->getName());
            $head->setSource('mobile');
            $head->setParent($parent);
            $head->setAccountMobileBank($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }

    }


    public function insertCustomerAccount(Customer $entity)
    {

        /* @var $exist AccountHead */
        $exist = $this->findOneBy(array('customer' => $entity));
        if ($exist) {
            $exist->setName($entity->getName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'account-receivable'));
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($entity->getName());
            $head->setSource('customer');
            $head->setParent($parent);
            $head->setCustomer($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }
    }

    public function insertConditionAccount(AccountCondition $entity)
    {

        /* @var $exist AccountHead */

        $exist = $this->findOneBy(array('accountCondition' => $entity));
        if ($exist) {
            $exist->setName($entity->getName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'account-receivable'));
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($entity->getName());
            $head->setSource('condition');
            $head->setParent($parent);
            $head->setAccountCondition($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }
    }

    public function insertLoanAccount(AccountLoanUser $entity)
    {

        /* @var $exist AccountHead */

        $exist = $this->findOneBy(array('accountLoanUser' => $entity));
        if ($exist) {
            $exist->setName($entity->getName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'account-payable'));
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($entity->getName());
            $head->setSource('loan');
            $head->setParent($parent);
            $head->setAccountLoanUser($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }
    }

    public function insertVendorAccount(AccountVendor $entity)
    {

        /* @var $exist AccountHead */
        $exist = $this->findOneBy(array('accountVendor' => $entity));
        if ($exist) {
            $exist->setName($entity->getCompanyName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'account-payable'));
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($entity->getCompanyName());
            $head->setSource('vendor');
            $head->setParent($parent);
            $head->setAccountVendor($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }
    }

    public function insertMedicineVendorAccount(MedicineVendor $entity)
    {

        /* @var $exist AccountHead */
        $exist = $this->findOneBy(array('medicineVendor' => $entity));
        if ($exist) {
            $exist->setName($entity->getCompanyName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'account-payable'));
            $head->setGlobalOption($entity->getMedicineConfig()->getGlobalOption());
            $head->setName($entity->getCompanyName());
            $head->setSource('vendor');
            $head->setParent($parent);
            $head->setMedicineVendor($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }
    }

    public function insertInventoryVendorAccount(Vendor $entity)
    {

        /* @var $exist AccountHead */
        $exist = $this->findOneBy(array('inventoryVendor' => $entity));
        if ($exist) {
            $exist->setName($entity->getCompanyName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'account-payable'));
            $head->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
            $head->setName($entity->getCompanyName());
            $head->setSource('vendor');
            $head->setParent($parent);
            $head->setInventoryVendor($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }
    }

    public function insertUserAccount(Profile $profile)
    {

        $entity = $profile->getUser();

        /* @var $exist AccountHead */

        $exist = $this->findOneBy(array('employee' => $entity));
        if ($exist) {
            $exist->setName($profile->getName());
            $exist->setSource('user');
            if($profile->getUserGroup() ==  "employee"){
                $parent = $this->findOneBy(array('slug' => 'salaries-expense'));
                $exist->setParent($parent);
            }elseif($profile->getUserGroup() ==  "stakeholder"){
                $parent = $this->findOneBy(array('slug' => 'capital-investment'));
                $exist->setParent($parent);
            }
            $this->_em->flush();
            return $exist;
        }else{
            $head = new AccountHead();
            if($profile->getUserGroup() ==  "employee"){
                $parent = $this->findOneBy(array('slug' => 'salaries-expense'));
                $head->setParent($parent);
            }elseif($profile->getUserGroup() ==  "stock-holder"){
                $parent = $this->findOneBy(array('slug' => 'capital-investment'));
                $head->setParent($parent);
            }elseif($profile->getUserGroup() ==  "stakeholder"){
                $parent = $this->findOneBy(array('slug' => 'capital-investment'));
                $head->setParent($parent);
            }
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($profile->getName());
            $head->setSource('user');
            $head->setEmployee($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }
    }

    public function insertCapitalAssetsAccount(GlobalOption $option ,PurchaseItem $entity)
    {

        /* @var $exist AccountHead */

        $exist = $this->findOneBy(array('assetsItem' => $entity->getItem()));
        if ($exist) {
            $exist->setName($entity->getItem()->getName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $head->setGlobalOption($option);
            $head->setName($entity->getItem()->getName());
            $head->setSource('Assets');
            $head->setAssetsItem($entity->getItem());
            $head->setParent($entity->getItem()->getCategory()->getAccountHead());
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }
    }

    public function insertBankSubHead($bank)
    {

        /* @var $exist AccountHead */
        $entity = $this->_em->getRepository('AccountingBundle:AccountBank')->find($bank);
        $exist = $this->findOneBy(array('accountBank' => $entity));
        if ($exist) {
            $exist->setName($entity->getName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'bank-account'));
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($entity->getName());
            $head->setSource('bank');
            $head->setParent($parent);
            $head->setAccountBank($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }

    }

    public function insertMobileSubHead($mobile)
    {

        $entity = $this->_em->getRepository('AccountingBundle:AccountMobileBank')->find($mobile);
        /* @var $exist AccountHead */
        $exist = $this->findOneBy(array('accountMobileBank' => $entity));
        if ($exist) {
            $exist->setName($entity->getName());
            $this->_em->flush();
            return $exist;
        } else {
            $head = new AccountHead();
            $parent = $this->findOneBy(array('slug' => 'mobile-account'));
            $head->setGlobalOption($entity->getGlobalOption());
            $head->setName($entity->getName());
            $head->setSource('mobile');
            $head->setParent($parent);
            $head->setAccountMobileBank($entity);
            $this->_em->persist($head);
            $this->_em->flush();
            return $head;
        }

    }



}