<?php
/**
 * Created by PhpStorm.
 * User: joasil
 * Date: 31-01-2016
 * Time: 20:46
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository {

    public function getFutureGames(){
        $d = new \DateTime();
        $query = $this->createQueryBuilder('g')
            ->select('g')
            ->Where('g.date >=:d')
            ->setParameter('d', $d)
            ->getQuery();
        return $query->getResult();
    }

}