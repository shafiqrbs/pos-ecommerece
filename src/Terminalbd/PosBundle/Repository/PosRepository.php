<?php
namespace Terminalbd\PosBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Terminalbd\PosBundle\Entity\Pos;
use Terminalbd\PosBundle\Entity\PosItem;

/**
 * VendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PosRepository extends EntityRepository
{

    public function posReset($option)
    {
        $em = $this->_em;
        $config = $option->getId();
        $history = $em->createQuery("DELETE PosBundle:Pos e WHERE e.terminal = {$config}");
        $history->execute();
    }

    public function insert(User $user){

        $em = $this->_em;
        $find = $this->findOneBy(array('createdBy' => $user , 'process' => 'Created'));
        $config = $user->getGlobalOption()->getInventoryConfig();
        $method = $em->getRepository("SettingToolBundle:TransactionMethod")->find(1);
        if(empty($find)){
            $entity = new Pos();
            $entity ->setCreatedBy($user);
            $entity ->setTransactionMethod($method);
            $entity ->setTerminal($user->getGlobalOption());
            if($config->getVatEnable() == 1){
                $vatPercentage = $config->getVatPercentage();
                $entity->setVatPercent($vatPercentage);
            }
            $em->persist($entity);
            $em->flush();
            return $entity;
        }elseif(empty($find->getTransactionMethod())){
            $find->setTransactionMethod($method);
            if($config->getVatEnable() == 1){
                $vatPercentage = $config->getVatPercentage();
                $find->setVatPercent($vatPercentage);
            }
            $em->persist($find);
            $em->flush();
            return $find;
        }
        return $find;

    }


    public function reset($user)
    {

        $em = $this->_em;
        /* @var $entity Pos */
        $entity = $this->findOneBy(array('createdBy' => $user , 'process' => 'Created'));
        if(empty($entity)){
            return $this->insert($user);
        }else{
            $entity->setSalesBy(null);
            $entity->setTransactionMethod(null);
            $entity->setSubTotal(0);
            $entity->setInvoice(null);
            $entity->setVat(0);
            $entity->setMode(null);
            $entity->setSd(0);
            $entity->setDue(0);
            $entity->setCustomer(null);
            $entity->setTotal(0);
            $entity->setPayment(0);
            $entity->setReturnAmount(0);
            $entity->setDeliveryCharge(0);
            $entity->setReceive(0);
            $entity->setDiscount(0);
            $entity->setSpecialDiscount(0);
            $entity->setDiscountCalculation(0);
            $entity->setDiscountType(null);
            $entity->setAccountBank(null);
            $entity->setAccountMobileBank(null);
            $entity->setTransactionId(null);
            $entity->setSalesBy(null);
            $em->persist($entity);
            $em->flush();
            return $entity;
        }


    }
    public function update($user,$cart)
    {

        $em = $this->_em;
        $config = $user->getGlobalOption()->getInventoryConfig();
        /* @var $find Pos */
        $find = $this->findOneBy(array('createdBy' => $user , 'process' => 'Created'));

        /* @var $entity Pos */
        $entity = $this->findOneBy(array('createdBy' => $user , 'process' => 'Created'));
        $entity->setSubTotal($cart->total());
        $discountCal = $find->getDiscountCalculation();
        if($discountCal > 0){
            $discount = ($entity->getSubTotal() * $discountCal)/100;
            $total = ($entity->getSubTotal() - $discount);
            $entity->setDiscountCalculation($discountCal);
            $entity->setDiscount(round($discount));
            $entity->setTotal(round($total));
            $entity->setDue(round($total));
        }else{
            $entity->setDiscount($cart->total() - ($cart->discount()));
            $total = (($entity->getSubTotal() + $entity->getDeliveryCharge() + $entity->getVat() + $entity->getSd()) -( $entity->getDiscount() +  $entity->getSpecialDiscount()));
            $entity->setTotal($total);
        }
        $em->persist($entity);
        $em->flush();
        return $entity;

    }

    public function insertHold(Pos $pos,$cart)
    {
        $em = $this->_em;
        foreach ($cart->contents() as $product){
            $entity = new PosItem();
            $entity->setPos($pos);
            $entity->setName($product['name']);
            $entity->setItemId($product['id']);
            $entity->setUnit($product['unit']);
            $entity->setPrice($product['price']);
            $entity->setQuantity($product['quantity']);
            $entity->setSubTotal($product['price'] * $product['quantity']);
            $em->persist($entity);
            $em->flush();
        }
        $pos->setProcess('Hold');
        $em->flush();
    }

    public function getLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(e.id)');
        $qb->from('InventoryBundle:Vendor','e');
        $qb->where("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }

    }

    public function getApiVendor(GlobalOption $entity)
    {

        $config = $entity->getInventoryConfig()->getId();
        $qb = $this->createQueryBuilder('s');
        $qb->where('s.inventoryConfig = :config')->setParameter('config', $config) ;
        $qb->orderBy('s.companyName','ASC');
        $result = $qb->getQuery()->getResult();

        $data = array();

        /* @var $row Vendor */

        foreach($result as $key => $row) {
            $data[$key]['vendor_id']    = (int) $row->getId();
            $data[$key]['name']           = $row->getCompanyName();
        }

        return $data;
    }


    public function searchAutoComplete($q, InventoryConfig $inventory)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.inventoryConfig', 'ic');
        $query->select('e.companyName as id');
        $query->addSelect('e.companyName as text');
        $query->where($query->expr()->like("e.companyName", "'$q%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.companyName', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

}
