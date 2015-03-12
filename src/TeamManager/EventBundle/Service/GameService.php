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
     *
     *
     * @param $playerID
     * @return array
     */
    public function getPlayerGames($playerID)
    {

    }

    /**
     *
     *
     * @param $playerID
     * @param $seasonID
     * @return array
     */
    public function getPlayerGamesForSeason($playerID, $seasonID)
    {

    }

    /**
     *
     *
     * @param $playerID
     * @return array
     */
    public function getPlayerFriendlyGames($playerID)
    {

    }

    /**
     *
     *
     * @param $playerID
     * @param $seasonID
     * @return array
     */
    public function getPlayerFriendlyGamesForSeason($playerID, $seasonID)
    {

    }

    /**
     *
     *
     * @param $teamID
     * @return array
     */
    public function getTeamGames($teamID)
    {

    }

    /**
     *
     *
     * @param $teamID
     * @param $seasonID
     * @return array
     */
    public function getTeamGamesForSeason($teamID, $seasonID)
    {

    }

    /**
     *
     *
     * @param $teamID
     * @return array
     */
    public function getTeamFriendlyGames($teamID)
    {

    }

    /**
     *
     *
     * @param $teamID
     * @param $seasonID
     * @return array
     */
    public function getTeamFriendlyGamesForSeason($teamID, $seasonID)
    {

    }

}