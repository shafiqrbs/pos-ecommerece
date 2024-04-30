<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{

    public function findWithSearch($inventory,$data)
    {

        $item = isset($data['item'])? $data['item'] :'';
        $category = isset($data['category'])? $data['category'] :'';

        $qb = $this->createQueryBuilder('masterItem');
        $qb->leftJoin('masterItem.category', 'category');
        $qb->where("masterItem.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        if (!empty($item)) {
            $qb->andWhere($qb->expr()->like("masterItem.name", "'%$item%'"  ));
        }

        if (!empty($category)) {
            $qb->andWhere("category.name = :name");
            $qb->setParameter('name', $category);
        }

        $qb->orderBy('masterItem.name','ASC');
        $qb->getQuery();
        return  $qb;

    }

    public function getLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(e)');
        $qb->from('InventoryBundle:Product','e');
        $qb->where("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }

    }

    public function searchAutoComplete($q, InventoryConfig $inventory)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.inventoryConfig', 'ic');
        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'%$q%'"  ));
        $query->orWhere($query->expr()->like("e.slug", "'%$q%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getArrayResult();

    }

    public function getProductCategories(InventoryConfig $inventory)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.inventoryConfig', 'ic');
        $query->join('e.category', 'category');
        $query->select('category.id as id');
        $query->addSelect('category.name as name');
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('e.category');
        $query->orderBy('category.name', 'ASC');
        return $query->getQuery()->getArrayResult();

    }

    public function getMasterItems(InventoryConfig $inventory)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.inventoryConfig', 'ic');
        $query->select('e.name as name','e.nameBn as nameBn');
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->orderBy('e.name', 'ASC');
        return $query->getQuery()->getArrayResult();

    }

    public function createNewProduct(InventoryConfig $inventory,$name)
    {
        $em = $this->_em;
        $entity = new Product();
        $entity->setInventoryConfig($inventory);
        $entity->setName($name);
        $em->persist($entity);
        $em->flush($entity);
        return $entity;
    }

    public function insertMasterItem(InventoryConfig $inventory,$data)
    {
        $em = $this->_em;
        $masterItem = $data['item']['name'];
        $find = $this->findOneBy(array('inventoryConfig'=>$inventory,'name' => $masterItem));
        if(empty($find)){
            $entity = new Product();
            $entity->setInventoryConfig($inventory);
            $entity->setName($masterItem);
            $category = isset($data['item']['category']) ? $data['item']['category'] :'';
            if($category){
                $cat = $em->getRepository("ProductProductBundle:Category")->findOneBy(array('inventoryConfig'=>$inventory,'name' => $category,'permission'=>'private'));
                $entity->setCategory($cat);
            }
            $itemUnit = isset($data['item']['itemUnit']) ? $data['item']['itemUnit'] :4;
            if($itemUnit){
                $unit = $em->getRepository("SettingToolBundle:ProductUnit")->find($itemUnit);
                $entity->setProductUnit($unit);
            }
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }else{
            return $find;
        }



    }



}