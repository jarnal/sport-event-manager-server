<?php

namespace TeamManager\ActionBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class CardService extends EntityRestService
{

    /**
     * {@inheritdoc}
     *
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
     * Retrieves all cards for a given player.
     */
    public function getPlayerCards($id)
    {
        return $this->repository->getCardsByPlayer($id);
    }

    /**
     *
     */
    public function getPlayerCardsForSeason($playerID, $season)
    {
        return $this->repository->getCardsByPlayerForSeason($playerID, $season);
    }

    /**
     * @param $playerID
     * @param $gameID
     * @return ArrayCollection
     */
    public function getPlayerCardsForGame($playerID, $gameID)
    {
        return $this->repository->getCardsByPlayerForGame($playerID, $gameID);
    }

    /**
     * Retrieves all cards for a given team.
     */
    public function getTeamCards($id)
    {
        return $this->repository->getCardsByTeam($id);
    }

    /**
     *
     */
    public function getTeamCardsForSeason($teamID, $season)
    {
        return $this->repository->getCardsByTeamForSeason($teamID, $season);
    }

    /*
     *
     */
    public function getTeamCardsForGame($teamID, $gameID)
    {
        return $this->repository->getCardsByTeamForGame($teamID, $gameID);
    }

    /**
     * Retrieves all cards for a given game.
     */
    public function getGameCards($id)
    {
        return $this->repository->getCardsByGame($id);
    }

}