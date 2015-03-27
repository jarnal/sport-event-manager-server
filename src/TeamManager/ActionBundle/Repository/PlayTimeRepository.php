<?php

namespace TeamManager\ActionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PlayTimeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayTimeRepository extends EntityRepository
{

    /**
     * @param $id
     */
    public function findOneById($id, $fullObject=true)
    {
        $query = $this->createQueryBuilder('playtime');
        $query->where('playtime.id = :id')
            ->setParameter(':id', $id)
        ;
        if($fullObject){
            $query->join('playtime.player', 'player')
                ->addSelect('player')
                ->join('playtime.game', 'game')
                ->addSelect('game')
            ;
        }

        $result = $query->getQuery()->getResult();
        if(isset($result[0])){
            return $result[0];
        }
        return $query->getQuery()->getResult();
    }

}
