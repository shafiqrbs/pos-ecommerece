<?php

namespace Setting\Bundle\ToolBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;

/**
 * ItemColorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductColorRepository extends EntityRepository
{
    public function getLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(cs)');
        $qb->from('InventoryBundle:ItemColor','e');
        $qb->where("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }

    }

    public function searchAutoComplete($q)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.item','item');
        $qb->select('e.name as id');
        $qb->addSelect('e.name as text');
        $qb->where('e.status = 1');
        $qb->andWhere($qb->expr()->like("e.name", "'$q%'"  ));
        $qb->groupBy('e.id');
        $qb->orderBy('e.name', 'ASC');
        $qb->setMaxResults( '10' );
        return $qb->getQuery()->getResult();

    }
}
