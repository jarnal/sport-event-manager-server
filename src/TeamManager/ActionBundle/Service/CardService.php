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
     * @param $playerID
     * @param $gameID
     * @return ArrayCollection
     */
    public function getCardsByPlayerForGame($playerID, $gameID)
    {
        return $this->repository->getCardsByPlayerForGame($playerID, $gameID);
    }

    /**
     * Retrieves all cards for a given team.
     */
    public function getTeamCards($id)
    {
        $this->repository->getCardsByTeam($id);
    }

    /**
     * Retrieves all cards for a given game.
     */
    public function getGameCards($id)
    {
        $this->repository->getCardsByGame($id);
    }

}