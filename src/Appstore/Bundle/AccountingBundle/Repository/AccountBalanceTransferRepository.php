<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * AccountJournalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountBalanceTransferRepository extends EntityRepository
{


    public function findWithSearch(User $user,$data = '')
    {
        $globalOption = $user->getGlobalOption();


        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $result = $qb->getQuery();
        return $result;
    }

	public function finalTransaction($globalOption)
	{
		//$globalOption = $user->getGlobalOption();

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.accountHeadDebit','d');
		$qb->join('e.accountHeadCredit','c');
		$qb->select('d.name as debitName ,c.name as creditName,e.transactionType as type , SUM(e.amount) AS amount');
		$qb->where("e.globalOption = :globalOption");
		$qb->setParameter('globalOption', $globalOption);
		$qb->andWhere("e.process = 'approved'");
		$qb->groupBy("e.accountHeadDebit,e.accountHeadCredit");
		//$qb->groupBy("e.transactionType,e.accountHeadDebit,e.accountHeadCredit");
		$result = $qb->getQuery()->getArrayResult();
		return $result;

	}

    public function accountCashOverview(User $user,$type,$data)
    {
        $globalOption = $user->getGlobalOption();

        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.toTransactionMethod','to');
        $qb->leftJoin('e.fromTransactionMethod','from');
        $qb->select('SUM(e.amount) AS amount');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = 'approved'");
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        $amount =  $result['amount'];
        return $amount;

    }

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {
        if(!empty($data))
        {
            $datetime = new \DateTime("now");
            $accountRefNo = isset($data['accountRefNo'])  ? $data['accountRefNo'] : '';
            $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
            $today_enddatetime = $datetime->format('Y-m-d 23:59:59');
            $startDate = isset($data['startDate']) and $data['startDate'] != '' ? $data['startDate'].' 00:00:00' : $today_startdatetime;
            $endDate =   isset($data['endDate']) and $data['endDate'] != '' ? $data['endDate'].' 23:59:59' : $today_enddatetime;


            if (!empty($accountRefNo)) {

                $qb->andWhere("e.accountRefNo = :accountRefNo");
                $qb->setParameter('accountRefNo', $accountRefNo);
            }

            if (!empty($data['startDate']) ) {

                $qb->andWhere("e.updated >= :startDate");
                $qb->setParameter('startDate', $startDate.' 00:00:00');
            }
            if (!empty($data['endDate'])) {

                $qb->andWhere("e.updated <= :endDate");
                $qb->setParameter('endDate', $endDate.' 23:59:59');
            }

        }

    }

    public function accountJournalOverview($globalOption,$data)
    {
        $qb = $this->_em->createQueryBuilder();
        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');

        $startDate = isset($data['startDate']) and $data['startDate'] != '' ? $data['startDate'].' 00:00:00' : $today_startdatetime;
        $endDate =   isset($data['endDate']) and $data['endDate'] != '' ? $data['endDate'].' 23:59:59' : $today_enddatetime;
        $toUser =    isset($data['toUser'])? $data['toUser'] :'';
        $accountHead = isset($data['accountHead'])? $data['accountHead'] :'';


        $qb->from('AccountingBundle:AccountJournal','s');
        $qb->select('sum(s.amount) as amount');
        $qb->where('s.globalOption = :globalOption');
        $qb->setParameter('globalOption', $globalOption);

        if (!empty($startDate) and $startDate !="") {
            $qb->andWhere("s.updated >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($endDate)) {
            $qb->andWhere("s.updated <= :endDate");
            $qb->setParameter('endDate', $endDate);
        }
        if (!empty($toUser)) {
            $qb->andWhere("s.toUser = :toUser");
            $qb->setParameter('toUser', $toUser);
        }
        if (!empty($accountHead)) {
            $qb->andWhere("s.accountHead = :accountHead");
            $qb->setParameter('accountHead', $accountHead);
        }

        $amount = $qb->getQuery()->getSingleScalarResult();
        return  $amount ;

    }

    public function reportOperatingRevenue($globalOption,$data){

        $parent = array(23,37);
        $qb = $this->createQueryBuilder('ex');
        $qb->join('ex.accountHeadCredit','accountHead');
        $qb->select('sum(ex.amount) as amount, accountHead.name as name');
        $qb->where("ex.parent IN (:parent)");
        $qb->setParameter('parent', $parent);
        $qb->andWhere('ex.globalOption = :globalOption');
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('accountHead.id');
        return  $qb->getQuery()->getArrayResult();
    }


    public function   insertAccountPurchaseJournal(Purchase $purchase)
    {
        $journalSource = "inventory-{$purchase->getId()}";
        $entity = new AccountJournal();
        $accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(49);
        $accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
        $accountBankHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(38);
        $accountMobileHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(45);

        $entity->setGlobalOption($purchase->getInventoryConfig()->getGlobalOption());
        $entity->setTransactionType('Debit');
        $entity->setAmount($purchase->getPaymentAmount());
        $entity->setTransactionMethod($purchase->getTransactionMethod());
        $entity->setAccountBank($purchase->getAccountBank());
        $entity->setAccountMobileBank($purchase->getAccountMobileBank());
        $entity->setApprovedBy($purchase->getApprovedBy());
        $entity->setCreatedBy($purchase->getApprovedBy());
        $entity->setAccountHeadCredit($accountHeadCredit);
        if ($purchase->getTransactionMethod()->getId() == 2){
            $entity->setAccountHeadDebit($accountBankHead);
        }elseif ($purchase->getTransactionMethod()->getId() == 3){
            $entity->setAccountHeadDebit($accountMobileHead);
        }else{
            $entity->setAccountHeadDebit($accountCashHead);
        }
        $entity->setToUser($purchase->getApprovedBy());
        $entity->setJournalSource($journalSource);
        $entity->setRemark("Inventory purchase as investment,Ref GRN no.{$purchase->getGrn()}");
        $entity->setProcess('approved');
        $this->_em->persist($entity);
        $this->_em->flush();
        return $entity;
    }

    public function removeApprovedPurchaseJournal(Purchase $purchase)
    {
        $option =  $purchase->getInventoryConfig()->getGlobalOption()->getId();
        $journalSource = "inventory-{$purchase->getId()}";
        $journal = $this->_em->getRepository('AccountingBundle:AccountJournal')->findOneBy(array('approvedBy' => $purchase->getApprovedBy(),'globalOption'=> $option ,'amount'=> $purchase->getPaymentAmount(),'journalSource' => $journalSource ));
        if(!empty($journal)) {
            $accountCash = $this->_em->getRepository('AccountingBundle:AccountCash')->findOneBy(array('processHead' => 'Journal', 'globalOption' => $option, 'accountRefNo' => $journal->getAccountRefNo()));
            if ($accountCash) {
                $this->_em->remove($accountCash);
                $this->_em->flush();
            }

            $transactions = $this->_em->getRepository('AccountingBundle:Transaction')->findBy(array('processHead' => 'Journal', 'globalOption' => $journal->getGlobalOption(), 'accountRefNo' => $journal->getAccountRefNo()));
            foreach ($transactions as $transaction) {
                if ($transaction) {
                    $this->_em->remove($transaction);
                    $this->_em->flush();
                }
            }
            $this->_em->remove($journal);
            $this->_em->flush();
        }
    }

    public function   insertAccountMedicinePurchaseJournal(MedicinePurchase $purchase)
    {

        $journalSource = "medicine-{$purchase->getId()}";
        $entity = new AccountJournal();
        $accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(49);
        $accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
        $accountBankHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(38);
        $accountMobileHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(45);

        $entity->setGlobalOption($purchase->getMedicineConfig()->getGlobalOption());
        $entity->setTransactionType('Debit');
        $entity->setAmount($purchase->getPayment());
        $entity->setTransactionMethod($purchase->getTransactionMethod());
        $entity->setAccountBank($purchase->getAccountBank());
        $entity->setAccountMobileBank($purchase->getAccountMobileBank());
        $entity->setApprovedBy($purchase->getApprovedBy());
        $entity->setCreatedBy($purchase->getApprovedBy());
        $entity->setAccountHeadCredit($accountHeadCredit);
        if ($purchase->getTransactionMethod()->getId() == 2){
            $entity->setAccountHeadDebit($accountBankHead);
        }elseif ($purchase->getTransactionMethod()->getId() == 3){
            $entity->setAccountHeadDebit($accountMobileHead);
        }else{
            $entity->setAccountHeadDebit($accountCashHead);
        }
        $entity->setToUser($purchase->getApprovedBy());
        $entity->setJournalSource($journalSource);
        $entity->setRemark("Medicine purchase as investment,Ref GRN no.{$purchase->getGrn()}");
        $entity->setProcess('approved');
        $this->_em->persist($entity);
        $this->_em->flush();
        return $entity;
    }

    public function removeApprovedMedicinePurchaseJournal(MedicinePurchase $purchase)
    {
        $option =  $purchase->getMedicineConfig()->getGlobalOption()->getId();
        $journalSource = "medicine-{$purchase->getId()}";
        $journal = $this->_em->getRepository('AccountingBundle:AccountJournal')->findOneBy(array('approvedBy' => $purchase->getApprovedBy(),'globalOption'=> $option,'journalSource' => $journalSource ));
        $em = $this->_em;
        if(!empty($journal)) {

                /* @var  $journal AccountJournal */

                $globalOption = $journal->getGlobalOption()->getId();
                $accountRefNo = $journal->getAccountRefNo();

                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
                $accountCash->execute();
                $journalRemove = $em->createQuery('DELETE AccountingBundle:AccountJournal e WHERE e.id = '.$journal->getId());
                if(!empty($journalRemove)){
                    $journalRemove->execute();
                }
        }

    }

	public function insertInventoryAccountSalesReturn(SalesReturn $salesReturn)
	{
		$global = $salesReturn->getInventoryConfig()->getGlobalOption();
		$accountSales = new AccountJournal();
		$accountSales->setGlobalOption($global);

		$journalSource = "Sales-Return-{$salesReturn->getSales()->getInvoice()}";
		$entity = new AccountJournal();
		$accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(34);
		$accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(31);
		$transaction = $this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1);

		$entity->setTransactionType('Credit');
		$entity->setAmount($salesReturn->getTotal());
		$entity->setTransactionMethod($transaction);
		$entity->setApprovedBy($salesReturn->getCreatedBy());
		$entity->setCreatedBy($salesReturn->getCreatedBy());
		$entity->setGlobalOption($salesReturn->getCreatedBy()->getGlobalOption());
		$entity->setAccountHeadCredit($accountHeadCredit);
		$entity->setAccountHeadDebit($accountCashHead);
		$entity->setToUser($salesReturn->getCreatedBy());
		$entity->setRemark("Inventory sales return as assets,Ref Invoice no-{$salesReturn->getSales()->getInvoice()}");
		$entity->setJournalSource($journalSource);
		$entity->setProcess('approved');
		$this->_em->persist($entity);
		$this->_em->flush();
		$this->_em->getRepository('AccountingBundle:AccountCash')->insertAccountCash($entity);
		$this->_em->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($entity);
		return $entity->getAccountRefNo();

	}


	public function insertMedicineAccountSalesReturn(MedicineSalesReturn $salesReturn)
	{
		$global = $salesReturn->getMedicineConfig()->getGlobalOption();
		$sales = $salesReturn->getMedicineSalesItem()->getMedicineSales();
		$accountSales = new AccountJournal();
		$accountSales->setGlobalOption($global);

		$journalSource = "Sales-Return-{$sales->getId()}";
		$entity = new AccountJournal();
		$accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(34);
		$accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(31);
		$transaction = $this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1);

     	$entity->setTransactionType('Credit');
		$entity->setAmount($salesReturn->getSubTotal());
		$entity->setTransactionMethod($transaction);
		$entity->setApprovedBy($salesReturn->getCreatedBy());
		$entity->setCreatedBy($salesReturn->getCreatedBy());
		$entity->setGlobalOption($salesReturn->getCreatedBy()->getGlobalOption());
		$entity->setAccountHeadCredit($accountHeadCredit);
		$entity->setAccountHeadDebit($accountCashHead);
		$entity->setToUser($salesReturn->getCreatedBy());
		$entity->setJournalSource($journalSource);
		$entity->setProcess('approved');
		$this->_em->persist($entity);
		$this->_em->flush();
		$this->_em->getRepository('AccountingBundle:AccountCash')->insertAccountCash($entity);
		$this->_em->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($entity);
		return $entity->getAccountRefNo();

	}


	public function insertAccountBusinessPurchaseJournal(BusinessPurchase $purchase)
	{

		$journalSource = "business-{$purchase->getId()}";
		$entity = new AccountJournal();
		$accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(49);
		$accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
		$accountBankHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(38);
		$accountMobileHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(45);

		$entity->setGlobalOption($purchase->getBusinessConfig()->getGlobalOption());
		$entity->setTransactionType('Debit');
		$entity->setAmount($purchase->getPayment());
		$entity->setTransactionMethod($purchase->getTransactionMethod());
		$entity->setAccountBank($purchase->getAccountBank());
		$entity->setAccountMobileBank($purchase->getAccountMobileBank());
		$entity->setApprovedBy($purchase->getApprovedBy());
		$entity->setCreatedBy($purchase->getApprovedBy());
		$entity->setAccountHeadCredit($accountHeadCredit);
		if ($purchase->getTransactionMethod()->getId() == 2){
			$entity->setAccountHeadDebit($accountBankHead);
		}elseif ($purchase->getTransactionMethod()->getId() == 3){
			$entity->setAccountHeadDebit($accountMobileHead);
		}else{
			$entity->setAccountHeadDebit($accountCashHead);
		}
		$entity->setToUser($purchase->getApprovedBy());
		$entity->setJournalSource($journalSource);
		$entity->setRemark("Business purchase as investment,Ref GRN no.{$purchase->getGrn()}");
		$entity->setProcess('approved');
		$this->_em->persist($entity);
		$this->_em->flush();
		return $entity;
	}

	public function removeApprovedBusinessPurchaseJournal(BusinessPurchase $purchase)
	{
		$option =  $purchase->getBusinessConfig()->getGlobalOption()->getId();
		$journalSource = "business-{$purchase->getId()}";
		$journal = $this->_em->getRepository('AccountingBundle:AccountJournal')->findOneBy(array('approvedBy' => $purchase->getApprovedBy(),'globalOption'=> $option,'journalSource' => $journalSource ));
		$em = $this->_em;
		if(!empty($journal)) {

			/* @var  $journal AccountJournal */

			$globalOption = $journal->getGlobalOption()->getId();
			$accountRefNo = $journal->getAccountRefNo();

			$transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
			$transaction->execute();
			$accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
			$accountCash->execute();
			$journalRemove = $em->createQuery('DELETE AccountingBundle:AccountJournal e WHERE e.id = '.$journal->getId());
			if(!empty($journalRemove)){
				$journalRemove->execute();
			}
		}

	}


}
