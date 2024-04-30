<?php

namespace Setting\Bundle\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SiteContentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SiteContentRepository extends EntityRepository
{

	public function getSubMenuList($sector,$parent){

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.parent','parent');
		$qb->select('e.name as name');
		$qb->addSelect('e.slug as slug');
		$qb->where('e.status = 1');
		$qb->andWhere("e.businessSector = :sector");
		$qb->setParameter('sector', $sector);
		$qb->andWhere("parent.slug = :slug");
		$qb->setParameter('slug', $parent);
		$results = $qb->getQuery()->getArrayResult();
		return $results;

	}

}
