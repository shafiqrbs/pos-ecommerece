<?php
namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountLoan;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * VendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountLoanUserRepository extends EntityRepository
{

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

        if(!empty($data))
        {
            $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
            $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
            $employee =    isset($data['employee'])? $data['employee'] :'';
            $process =    isset($data['process'])? $data['process'] :'';
            $user =    isset($data['user'])? $data['user'] :'';
            if (!empty($employee)) {
                $qb->andWhere("e.employee = :employee")->setParameter('employee', $employee);
            }
            if (!empty($process)) {
                $qb->andWhere("e.process = :process")->setParameter('process', $process);
            }
            if (!empty($user)) {
                $qb->join('e.createdBy','user');
                $qb->andWhere("user.id = :user")->setParameter('user', $user);
            }
            if (!empty($startDate) ) {
                $start = date('Y-m-d 00:00:00',strtotime($data['startDate']));
                $qb->andWhere("e.updated >= :startDate");
                $qb->setParameter('startDate', $start);
            }
            if (!empty($endDate)) {
                $end = date('Y-m-d 23:59:59',strtotime($data['endDate']));
                $qb->andWhere("e.updated <= :endDate");
                $qb->setParameter('endDate',$end);
            }
        }
    }

}