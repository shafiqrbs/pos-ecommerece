<?php

namespace Core\UserBundle\Entity\Repository;

use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    public function getAll()
    {
        return $this->findAll();
    }

    public function create($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
    }

    public function delete($data)
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }

    public function update($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
        return $this->_em;
    }

    public function searchAutoComplete($q, GlobalOption $globalOption)
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.username as id');
        $query->addSelect('e.username as text');
        $query->where($query->expr()->like("e.username", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.username', 'ASC');
        $query->setMaxResults( '100' );
        return $query->getQuery()->getResult();

    }

    public function searchAutoCompleteProfile($q, GlobalOption $globalOption)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.profile','p');
        $query->select('p.name as id');
        $query->addSelect('p.name as text');
        $query->where("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        if (!empty($q)) {
            $query->andWhere('e.username LIKE :searchTerm OR p.name LIKE :searchTerm OR p.mobile LIKE :searchTerm');
            $query->setParameter('searchTerm', '%'.trim($q).'%');
        }
        $query->groupBy('e.id');
        $query->orderBy('p.name', 'ASC');
        $query->setMaxResults( '100' );
        return $query->getQuery()->getResult();

    }

    public function newExistingCustomerForSales($globalOption,$mobile,$data)
    {
        $em = $this->_em;
        $name = $data['customerName'];
        $address = isset($data['customerAddress']) ? $data['customerAddress']:'';
        $email = isset($data['customerEmail']) ? $data['customerEmail']:'';
        $entity = $em->getRepository('UserBundle:User')->findOneBy(array('globalOption' => $globalOption ,'username' => $mobile));
        if($entity){
            $profile = $entity->getProfile();
            $profile->setUser($entity);
            $profile->setMobile($mobile);
            $profile->setName($name);
            $profile->setAddress($address);
            $em->persist($profile);
            $em->flush();
            return $entity;
        }else{
            $entity = new User();
            $entity->setUsername($mobile);
            $email = "{$mobile}@gmail.com";
            $entity->setEmail($email);
            $entity->setEnabled(1);
            $entity->setUserGroup('customer');
            $entity->setRoles(array('ROLE_CUSTOMER'));
            $entity->setGlobalOption($globalOption);
            $entity->setPlainPassword($mobile);
            $em->persist($entity);
            $em->flush();

            $profile = new Profile();
            $profile->setUser($entity);
            $profile->setMobile($mobile);
            $profile->setName($name);
            $profile->setAddress($address);
            $em->persist($profile);
            $em->flush();
            return $entity;
        }
    }

    /**
     * @param array $criteria
     * @return array
     */
    public function getEntityByIdAndStatusCriteria(array $criteria)
    {
        if ( $criteria['username']) {
            return $this->createQueryBuilder('e')
                ->andWhere('e.username = :username')
                ->setParameter('username', $criteria['username'])
                ->getQuery()
                ->getResult();
        }

        return [];
    }

    public function checkExistingUser($mobile)
    {
        return (boolean)$this->createQueryBuilder('u')
            ->andWhere('u.username = :user')
            ->setParameter('user', $mobile)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAccessRoleGroup(GlobalOption $globalOption){


        $modules = $globalOption->getSiteSetting()->getAppModules();
        $arrSlugs = array();
        if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
            foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $arrSlugs[] = $mod->getSlug();
                }
            }
        }

        $array = array();

        $inventory = array('inventory');
        $result = array_intersect($arrSlugs, $inventory);
        if (!empty($result)) {

            $array['Inventory'] = array(
                'ROLE_INVENTORY'                                    => 'Inventory',
                'ROLE_DOMAIN_INVENTORY_SALES'                       => 'Inventory Sales',
                'ROLE_DOMAIN_INVENTORY_DUE'                         => 'Inventory Due Receive',
                'ROLE_DOMAIN_INVENTORY'                             => 'Inventory Domain',
                'ROLE_DOMAIN_INVENTORY_PURCHASE'                    => 'Inventory Purchase',
                'ROLE_DOMAIN_INVENTORY_CUSTOMER'                    => 'Inventory Customer',
                'ROLE_DOMAIN_INVENTORY_APPROVAL'                    => 'Inventory Approval',
                'ROLE_DOMAIN_INVENTORY_REVERSE'                     => 'Purchase/Sales Reverse',
                'ROLE_DOMAIN_INVENTORY_STOCK'                       => 'Inventory Stock',
                'ROLE_DOMAIN_INVENTORY_REPORT'                      => 'Inventory Report',
                'ROLE_DOMAIN_INVENTORY_BRANCH'                      => 'Inventory Branch',
                'ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER'              => 'Inventory Branch Manager',
                'ROLE_DOMAIN_INVENTORY_MANAGER'                     => 'Inventory Manager',
                'ROLE_DOMAIN_INVENTORY_CONFIG'                      => 'Inventory Config',
                'ROLE_DOMAIN_INVENTORY_ADMIN'                       => 'Inventory Admin',
            );
        }

        $accounting = array('accounting');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result)) {

            $array['Accounting'] = array(
                'ROLE_ACCOUNTING'                               => 'Accounting',
                'ROLE_DOMAIN_ACCOUNTING_EXPENDITURE'            => 'Expenditure',
                'ROLE_DOMAIN_ACCOUNTING_PURCHASE'               => 'Purchase',
                'ROLE_DOMAIN_ACCOUNTING_SALES'                  => 'Sales',
                'ROLE_DOMAIN_ACCOUNTING_EXPENDITURE_PURCHASE'   => 'Expenditure Purchase',
                'ROLE_DOMAIN_ACCOUNTING_CASH'                   => 'Account Cash',
                'ROLE_DOMAIN_ACCOUNTING_JOURNAL'                => 'Journal',
                'ROLE_DOMAIN_ACCOUNTING_TRANSACTION'            => 'Transaction',
                'ROLE_DOMAIN_ACCOUNTING_PURCHASE_REPORT'        => 'Purchase Report',
                'ROLE_DOMAIN_ACCOUNTING_SALES_REPORT'           => 'Sales Report',
                'ROLE_DOMAIN_ACCOUNTING_REPORT'                 => 'Financial Report',
                'ROLE_DOMAIN_ACCOUNTING_SALES_ADJUSTMENT'       => 'Cash Adjustment',
                'ROLE_DOMAIN_ACCOUNTING_RECONCILIATION'         => 'Cash Reconciliation',
                'ROLE_DOMAIN_ACCOUNTING_CONDITION'              => 'Condition Account',
                'ROLE_DOMAIN_ACCOUNTING_BANK'                   => 'Bank & Mobile',
                'ROLE_DOMAIN_FINANCE_APPROVAL'                  => 'Approval',
                'ROLE_DOMAIN_ACCOUNTING_LOAN'                   => 'Loan',
                'ROLE_DOMAIN_ACCOUNT_REVERSE'                   => 'Reverse',
                'ROLE_DOMAIN_ACCOUNTING_CONFIG'                 => 'Configuration',
                'ROLE_DOMAIN_ACCOUNTING'                        => 'Admin',
            );
        }


        $pos = array('inventory','miss','business','restaurant');
        $result = array_intersect($arrSlugs, $pos);
        if (!empty($result)) {
            $array['POS'] = array(
                'ROLE_POS'                                      => 'POS',
                'ROLE_POS_MANAGER'                              => 'POS Manager',
                'ROLE_POS_ADMIN'                                => 'POS Admin'
            );
        }


        $array['Customer'] = array(
            'ROLE_CRM'                          => 'Customer',
            'ROLE_CUSTOMER_REHAB'               => 'Rehab Patient',
            'ROLE_CRM_MANAGER'                  => 'Manage Customer ',
            'ROLE_MEMBER_ASSOCIATION'           => 'Association',
            'ROLE_MEMBER_ASSOCIATION_VIEWER'    => 'Association Viewer',
            'ROLE_MEMBER_ASSOCIATION_MODERATOR' => 'Association Moderator',
            'ROLE_MEMBER_ASSOCIATION_ADMIN'     => 'Association Admin',
        );


        $ecommerce = array('e-commerce');
        $result = array_intersect($arrSlugs, $ecommerce);
        if (!empty($result)) {

            $array['E-commerce'] = array(
                'ROLE_ECOMMERCE'                            => 'E-commerce',
                'ROLE_DOMAIN_ECOMMERCE_PRODUCT'             => 'E-commerce Product',
                'ROLE_DOMAIN_ECOMMERCE_ORDER'               => 'E-commerce Order',
                'ROLE_DOMAIN_ECOMMERCE_PURCHASE'            => 'E-commerce Purchase',
                'ROLE_DOMAIN_ECOMMERCE_MANAGER'             => 'E-commerce Manager',
                'ROLE_DOMAIN_ECOMMERCE_VENDOR'              => 'E-commerce Vendor',
                'ROLE_DOMAIN_ECOMMERCE_REPORT'              => 'E-commerce Report',
                'ROLE_DOMAIN_ECOMMERCE_CONFIG'              => 'E-commerce Admin',
            );
        }

        $appearance = array('website','e-commerce');
        $result = array_intersect($arrSlugs, $appearance);
        if (!empty($result)) {

            $array['Appearance'] = array(

                'ROLE_APPEARANCE'               => 'Appearance',
                'ROLE_DOMAIN_ECOMMERCE_MENU'    => 'E-commerce Menu',
                'ROLE_DOMAIN_ECOMMERCE_SETTING' => 'E-commerce Setting',
                'ROLE_DOMAIN_ECOMMERCE_WEDGET'  => 'E-commerce Wedget',
                'ROLE_DOMAIN_WEBSITE_WEDGET'    => 'Website Wedget',
                'ROLE_DOMAIN_WEBSITE_SETTING'   => 'Website Setting',

            );
        }

        $array['Reports'] = array(
            'ROLE_REPORT'                        => 'Reports',
            'ROLE_REPORT_FINANCIAL'              => 'Accounting Financial',
            'ROLE_REPORT_SALES'                  => 'Sales Reports',
            'ROLE_REPORT_MANAGEMENT'             => 'Management',
            'ROLE_REPORT_ADMIN'                  => 'Admin',
        );

	    $array['SMS'] = array(
            'ROLE_SMS'                                          => 'Sms/E-mail',
            'ROLE_SMS_MANAGER'                                  => 'Sms/E-mail Manager',
            'ROLE_SMS_CONFIG'                                   => 'SMS/E-mail Setup',
            'ROLE_SMS_BULK'                                     => 'SMS Bulk',

        );
        return $array;
    }

    public function getAndroidRoleGroup(){

        $array = array();
        $array['Android Apps'] = array(
            'ROLE_MANAGER'                                   => 'Manager',
            'ROLE_PURCHASE'                                  => 'Purchase',
            'ROLE_SALES'                                     => 'Sales',
            'ROLE_EXPENSE'                                   => 'Expense',
            'ROLE_STOCK'                                     => 'Stock',
        );


        return $array;
    }

    public function getEmployees(GlobalOption $option, $data = [])
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.profile','p');
        $qb->leftJoin('p.location','l');
        $qb->leftJoin('e.employeePayroll','ep');
        $qb->leftJoin('p.designation','d');
        $qb->leftJoin('ep.approvedBy','epa');
        $qb->select('e.id as id','e.username as username');
        $qb->addSelect('d.name as designationName');
        $qb->addSelect('l.name as locationName');
        $qb->addSelect('epa.id as epaId','epa.username as epaUsername');
        $qb->addSelect('p.name as name','p.mobile as mobile','p.address as address','p.employeeType as employeeType','p.joiningDate as joiningDate','p.userGroup as userGroup');
        $qb->addSelect('ep.basicAmount as basicAmount','ep.allowanceAmount as allowance','ep.deductionAmount as deduction','ep.loanAmount as loan','ep.advanceAmount as advance','ep.arearAmount as arear','ep.salaryType as salaryType','ep.totalAmount as total','ep.payableAmount as payable');
        $qb->where("e.globalOption =".$option->getId());
        $qb->andWhere('e.domainOwner = 2');
        $qb->andWhere('e.isDelete != 1');
        $qb->orderBy("p.name","ASC");
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }


    public function getEmployeeEntities(GlobalOption $option)
    {

        $qb = $this->createQueryBuilder('e');
        $array = array('user');
        $qb->where("e.globalOption =".$option->getId());
        $qb->andWhere("e.userGroup IN (:userGroups)")->setParameter('userGroups',$array);
      //  $qb->andWhere('e.isDelete != 1');
        $qb->orderBy("e.username","ASC");
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function insertAccountUser(GlobalOption $option,$mobile,$data)
    {
        $user = new User();
        $em = $this->_em;
        $email = $mobile."@gmail.com";
        $user->setUsername($mobile);
        $user->setEmail($email);
        $user->setPassword('@123456');
        $user->setUserGroup('account');
        $user->setGlobalOption($option);
        $user->setDomainOwner(2);
        $em->persist($user);
        $em->flush();
        return $user;

    }

    public function getCustomers(GlobalOption $option){


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.profile','p');
        $qb->join('e.globalOption','g');
        $qb->select('e.username as username','e.email as email', 'e.id as id', 'e.appPassword as appPassword', 'e.appRoles as appRoles');
        $qb->addSelect('p.name as fullName');
        $qb->where("e.globalOption =".$option->getId());
        $qb->andWhere("g.status =1");
        $qb->andWhere('e.domainOwner = 2');
        $qb->andWhere('e.enabled = 1');
        $qb->andWhere('e.isDelete != 1');
        $qb->orderBy("p.name","ASC");
        $result = $qb->getQuery()->getArrayResult();
        $data =array();
        if($result){
            foreach($result as $key => $row){
                $roles = unserialize(serialize($row['appRoles']));
                $rolesSeparated = implode(",", $roles);
                $data[$key]['user_id'] = (int) $row['id'];
                $data[$key]['username'] = $row['username'];
                $data[$key]['fullName'] = $row['fullName'];
                $data[$key]['email'] = $row['email'];
                $data[$key]['password'] = $row['appPassword'];
                $data[$key]['roles'] = $rolesSeparated;

            }
        }

        return $data;

    }


    public function getOTP(GlobalOption $option,$data){


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.profile','p');
        $qb->join('e.globalOption','g');
        $qb->select('e.username as username','e.email as email', 'e.id as id', 'e.appRoles as appRoles');
        $qb->addSelect('p.name as fullName');
        $qb->where("e.globalOption =".$option->getId());
        $qb->andWhere("e.username = :username")->setParameter('username',$data['mobile']);
        $qb->andWhere("g.status =1");
        $qb->andWhere("e.userGroup = 'customer'");
        $qb->andWhere('e.enabled = 1');
        $user = $qb->getQuery()->getOneOrNullResult();
        $a = mt_rand(1000,9999);
        $user->setPlainPassword($a);
        $user->setappPassword($a);
        $this->get('fos_user.user_manager')->updateUser($user);
        $data = array();
        return $data;

    }

    public function getAndroidOTP($mobile){

        $em = $this->_em;
        $user = $this->findOneBy(array('username'=>$mobile));
        if($user){
            $a = mt_rand(1000,9999);
            $user->setPlainPassword($a);
            $user->setappPassword($a);
            $em->persist($user);
            $em->flush();
            return $user;
        }
        return false;


    }



    public function androidUserCreate(GlobalOption $setup,$data)
    {
        $em = $this->_em;
        $mobile = isset($data['mobile']) ? $data['mobile'] :'';
        $name = isset($data['name']) ? $data['name'] :'';
        $password = "8148148#";
        $entity = new User();
        $entity->setGlobalOption($setup);
        $entity->setEnabled(true);
        $entity->setDomainOwner(1);
        $entity->setUsername($mobile);
        $entity->setEmail($mobile.'@gmail.com');
        $entity->setPlainPassword($password);
        $entity->setAppPassword($password);
        if(empty($data['role'])){
            $entity->setRoles(array('ROLE_DOMAIN'));
            $entity->setDomainOwner(1);
        }else{
            $entity->setRoles(array('ROLE_MEDICINE,ROLE_MEDICINE_SALES'));
            $entity->setDomainOwner(2);
        }
        $roels = array('ROLE_MANAGER,ROLE_PURCHASE,ROLE_SALES,ROLE_EXPENSE,ROLE_STOCK');
        $entity->setAppRoles($roels);
        $em->persist($entity);
        $em->flush();

        $profile = new Profile();
        $profile->setUser($entity);
        $profile->setName($name);
        $profile->setMobile($mobile);
        $em->persist($profile);
        $em->flush();
        return $entity;

    }




}