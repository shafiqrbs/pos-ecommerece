<?php

namespace Appstore\Bundle\EcommerceBundle\EventListener;

use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ItemListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Order" entity
        if ($entity instanceof Item) {

            echo $lastCode = $this->getLastCode($args, $entity);
            exit;
            $entity->setCode($lastCode+1);
            $entity->setSku(sprintf("%s",str_pad($entity->getCode(),5, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $datetime
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args, Item $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('EcommerceBundle:Item')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.ecommerceConfig = :option')
            ->setParameter('option', $entity->getEcommerceConfig()->getId());
        $lastCode = $qb->getQuery()->getSingleScalarResult();
        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }
}