<?php

namespace TeamManager\EventBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TrainingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TrainingRepository extends EntityRepository
{

    /**
     *
     */
    public function findAll()
    {
        $query = $this->createQueryBuilder('training');
        $query->join('training.location', 'location')
            ->addSelect('location')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @param $id
     */
    public function findOneById($id)
    {
        $query = $this->createQueryBuilder('training');
        $query->leftJoin('training.expected_players', 'expected_players')
            ->leftJoin('training.missing_players', 'missing_players')
            ->leftJoin('training.present_players', 'present_players')
            ->join('training.location', 'location')
            ->join('training.team', 'team')
            ->addSelect('team')
            ->addSelect('expected_players')
            ->addSelect('missing_players')
            ->addSelect('present_players')
            ->addSelect('location')
            ->where('training.id = :id')
            ->setParameter(':id', $id)
        ;
        $result = $query->getQuery()->getResult();
        if(isset($result[0])){
            return $result[0];
        }
        return $query->getQuery()->getResult();
    }

    /**
     *
     *
     * @param $playerID
     * @return array
     */
    public function findTrainingsByPlayer($playerID)
    {
        $query = $this->createQueryBuilder('training');
        $query->innerjoin('training.team', 'team')
            ->innerjoin('team.players', 'player', 'WITH', $query->expr()->eq('player.id', $playerID))
            ->addSelect("team")
            ->innerJoin("training.location", "location")
            ->addSelect("location")
            ->orderBy('training.date', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     *
     *
     * @param $playerID
     * @param $seasonID
     * @return array
     */
    public function findTrainingsForPlayerBySeason($playerID, $season)
    {
        $query = $this->createQueryBuilder('training');
        $query->innerjoin('training.team', 'team')
            ->innerjoin('team.players', 'player', 'WITH', $query->expr()->eq('player.id', $playerID))
            ->addSelect("team")
            ->innerJoin("training.location", "location")
            ->addSelect("location")
            ->where('training.season = :season')
            ->setParameter("season", $season)
            ->orderBy('training.date', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     *
     *
     * @param $teamID
     * @return array
     */
    public function findTrainingsByTeam($teamID)
    {
        $query = $this->createQueryBuilder('training');
        $query->innerjoin('training.team', 'team', 'WITH', $query->expr()->eq('team.id', $teamID))
            ->innerJoin("training.location", "location")
            ->addSelect("location")
            ->orderBy('training.date', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     *
     *
     * @param $teamID
     * @param $seasonID
     * @return array
     */
    public function findTrainingsForTeamBySeason($teamID, $season)
    {
        $query = $this->createQueryBuilder('training');
        $query->innerjoin('training.team', 'team', 'WITH', $query->expr()->eq('team.id', $teamID))
            ->innerJoin("training.location", "location")
            ->addSelect("location")
            ->andWhere('training.season = :season')
            ->setParameter('season', $season)
            ->orderBy('training.date', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

}
