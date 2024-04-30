<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * InventoryConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InventoryConfigRepository extends EntityRepository
{


    public function inventoryReset(GlobalOption $option)
    {

        $em = $this->_em;
        $config = $option->getInventoryConfig()->getId();

        $StockItem = $em->createQuery('DELETE InventoryBundle:StockItem e WHERE e.inventoryConfig = '.$config);
        $StockItem->execute();

        $Damage = $em->createQuery('DELETE InventoryBundle:Damage e WHERE e.inventoryConfig = '.$config);
        $Damage->execute();

        $ExcelImporter = $em->createQuery('DELETE InventoryBundle:ExcelImporter e WHERE e.inventoryConfig = '.$config);
        $ExcelImporter->execute();

        $SalesReturn = $em->createQuery('DELETE InventoryBundle:SalesReturn e WHERE e.inventoryConfig = '.$config);
        $SalesReturn->execute();

        $Delivery = $em->createQuery('DELETE InventoryBundle:Delivery e WHERE e.inventoryConfig = '.$config);
        $Delivery->execute();

        $Sales = $em->createQuery('DELETE InventoryBundle:Sales e WHERE e.inventoryConfig = '.$config);
        $Sales->execute();

        $android = $em->createQuery('DELETE InventoryBundle:InventoryAndroidProcess e WHERE e.inventoryConfig = '.$config);
        $android->execute();

        $SalesImport = $em->createQuery('DELETE InventoryBundle:SalesImport e WHERE e.inventoryConfig = '.$config);
        $SalesImport->execute();

        $PurchaseReturn = $em->createQuery('DELETE InventoryBundle:PurchaseReturn e WHERE e.inventoryConfig = '.$config);
        $PurchaseReturn->execute();

        $PurchaseVendorItem = $em->createQuery('DELETE InventoryBundle:PurchaseVendorItem e WHERE e.inventoryConfig = '.$config);
        $PurchaseVendorItem->execute();

        $Purchase = $em->createQuery('DELETE InventoryBundle:Purchase e WHERE e.inventoryConfig = '.$config);
        $Purchase->execute();

        $stockAdjustment = $em->createQuery('DELETE InventoryBundle:ItemStockAdjustment e WHERE e.config = '.$config);
        $stockAdjustment->execute();


        $Item = $em->createQuery('DELETE InventoryBundle:Item e WHERE e.inventoryConfig = '.$config);
       // $Item->execute();

        $Purchase = $em->createQuery('DELETE InventoryBundle:Product e WHERE e.inventoryConfig = '.$config);
       // $Purchase->execute();

        $ItemBrand = $em->createQuery('DELETE InventoryBundle:ItemBrand e WHERE e.inventoryConfig = '.$config);
      //  $ItemBrand->execute();

        $qb = $this->_em->createQueryBuilder();
        $q = $qb->update('InventoryBundle:Item', 's')
            ->set('s.remainingQnt', '?1')
            ->set('s.purchaseQuantity', '?2')
            ->set('s.purchaseQuantityReturn', '?3')
            ->set('s.salesQuantity', '?4')
            ->set('s.salesQuantityReturn', '?5')
            ->set('s.damageQuantity', '?6')
            ->set('s.minQnt', '?9')
            ->set('s.purchaseAvgPrice', '?11')
            ->set('s.salesAvgPrice', '?12')
            ->set('s.openingQuantity', '?13')
            ->set('s.adjustmentQuantity', '?14')
            ->set('s.quantity', '?15')
            ->set('s.discountPrice', '?16')
            ->set('s.price', '?17')
            ->where('s.inventoryConfig = ?10')
            ->setParameter(1, 0)
            ->setParameter(2, 0)
            ->setParameter(3, 0)
            ->setParameter(4, 0)
            ->setParameter(5, 0)
            ->setParameter(6, 0)
            ->setParameter(9, 0)
            ->setParameter(11, 0)
            ->setParameter(12, 0)
            ->setParameter(13, 0)
            ->setParameter(14, 0)
            ->setParameter(15, 0)
            ->setParameter(16, 0)
            ->setParameter(17, 0)
            ->setParameter(10, $config)
            ->getQuery();
        $q->execute();
    }

    public function inventoryDelete(GlobalOption $option)
    {

        $em = $this->_em;
        $config = $option->getInventoryConfig()->getId();

        $StockItem = $em->createQuery('DELETE InventoryBundle:StockItem e WHERE e.inventoryConfig = '.$config);
        $StockItem->execute();

        $Damage = $em->createQuery('DELETE InventoryBundle:Damage e WHERE e.inventoryConfig = '.$config);
        $Damage->execute();

        $ExcelImporter = $em->createQuery('DELETE InventoryBundle:ExcelImporter e WHERE e.inventoryConfig = '.$config);
        $ExcelImporter->execute();

        $SalesReturn = $em->createQuery('DELETE InventoryBundle:SalesReturn e WHERE e.inventoryConfig = '.$config);
        $SalesReturn->execute();

        $Delivery = $em->createQuery('DELETE InventoryBundle:Delivery e WHERE e.inventoryConfig = '.$config);
        $Delivery->execute();

        $Sales = $em->createQuery('DELETE InventoryBundle:Sales e WHERE e.inventoryConfig = '.$config);
        $Sales->execute();

        $android = $em->createQuery('DELETE InventoryBundle:InventoryAndroidProcess e WHERE e.inventoryConfig = '.$config);
        $android->execute();

        $SalesImport = $em->createQuery('DELETE InventoryBundle:SalesImport e WHERE e.inventoryConfig = '.$config);
        $SalesImport->execute();

        $PurchaseReturn = $em->createQuery('DELETE InventoryBundle:PurchaseReturn e WHERE e.inventoryConfig = '.$config);
        $PurchaseReturn->execute();

        $PurchaseVendorItem = $em->createQuery('DELETE InventoryBundle:PurchaseVendorItem e WHERE e.inventoryConfig = '.$config);
        $PurchaseVendorItem->execute();

        $Purchase = $em->createQuery('DELETE InventoryBundle:Purchase e WHERE e.inventoryConfig = '.$config);
        $Purchase->execute();



        $stockAdjustment = $em->createQuery('DELETE InventoryBundle:ItemStockAdjustment e WHERE e.config = '.$config);
        $stockAdjustment->execute();


        $Item = $em->createQuery('DELETE InventoryBundle:Item e WHERE e.inventoryConfig = '.$config);
        $Item->execute();

        $Purchase = $em->createQuery('DELETE InventoryBundle:Product e WHERE e.inventoryConfig = '.$config);
        $Purchase->execute();

        $ItemBrand = $em->createQuery('DELETE InventoryBundle:ItemBrand e WHERE e.inventoryConfig = '.$config);
        $ItemBrand->execute();

    }

}
