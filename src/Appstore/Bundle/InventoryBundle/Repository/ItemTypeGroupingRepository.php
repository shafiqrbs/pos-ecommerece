<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;

/**
 * ItemTypeGroupingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemTypeGroupingRepository extends EntityRepository
{
    public function getItemTypeTree()
    {

    }

    public function searchAutoComplete($q, InventoryConfig $inventory)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.categories', 'categories');
        $query->select('categories.name as id');
        $query->addSelect('categories.name as text');
        $query->where($query->expr()->like("categories.name", "'$q%'"  ));
        $query->andWhere("e.inventoryConfig = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('e.id');
        $query->orderBy('categories.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }
}