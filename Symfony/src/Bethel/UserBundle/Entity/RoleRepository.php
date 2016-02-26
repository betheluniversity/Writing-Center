<?php

namespace Bethel\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * RoleRepository
 *
 * Gives functionality to the role object.
 */
class RoleRepository extends EntityRepository {

	public function getRoleByName($roleName){
		$qb = $this->createQueryBuilder('r')
            ->where('r.role LIKE :role')
            ->setParameter('role', '%'.$roleName.'%');

        return $qb->getQuery()->getSingleResult();
	}

}