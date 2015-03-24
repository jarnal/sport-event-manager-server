<?php

namespace TeamManager\EventBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class GameService extends EntityRestService
{

    /**
     * @param ObjectManager $pEntityManager
     * @param FormFactoryInterface $pFormFactory
     * @param $pEntityClass
     * @param $pFormTypeClass
     * @param $pFormExceptionClass
     */
    public function __construct(ObjectManager $pEntityManager, FormFactoryInterface $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass)
    {
        parent::__construct($pEntityManager, $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass);
    }

    /**
     * {@inheritdoc}
     */
    protected function processForm($entity, array $pParameters, $pMethod = "PUT")
    {
        $eventType = $entity->isFriendly()? "game_friendly" : "game";
        $entity->setType($eventType);

        return parent::processForm($entity, $pParameters, $pMethod);
    }

    /**
     *
     *
     * @param $playerID
     * @return array
     */
    public function getPlayerGames($playerID)
    {
        return $this->repository->findGamesByPlayer($playerID);
    }

    /**
     *
     *
     * @param $playerID
     * @param $seasonID
     * @return array
     */
    public function getPlayerGamesForSeason($playerID, $season)
    {
        return $this->repository->findGamesForPlayerBySeason($playerID, $season);
    }

    /**
     *
     *
     * @param $playerID
     * @return array
     */
    public function getPlayerFriendlyGames($playerID)
    {
        return $this->repository->findFriendlyGamesByPlayer($playerID);
    }

    /**
     *
     *
     * @param $playerID
     * @param $seasonID
     * @return array
     */
    public function getPlayerFriendlyGamesForSeason($playerID, $season)
    {
        return $this->repository->findFriendlyGamesForPlayerBySeason($playerID, $season);
    }

    /**
     *
     *
     * @param $teamID
     * @return array
     */
    public function getTeamGames($teamID)
    {
        return $this->repository->findGamesByTeam($teamID);
    }

    /**
     *
     *
     * @param $teamID
     * @param $seasonID
     * @return array
     */
    public function getTeamGamesForSeason($teamID, $season)
    {
        return $this->repository->findGamesForTeamBySeason($teamID, $season);
    }

    /**
     *
     *
     * @param $teamID
     * @return array
     */
    public function getTeamFriendlyGames($teamID)
    {
        return $this->repository->findFriendlyGamesByTeam($teamID);
    }

    /**
     *
     *
     * @param $teamID
     * @param $seasonID
     * @return array
     */
    public function getTeamFriendlyGamesForSeason($teamID, $season)
    {
        return $this->repository->findFriendlyGamesForTeamBySeason($teamID, $season);
    }

}