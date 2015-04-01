<?php

namespace TeamManager\ActionBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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
     * {@inheritdoc}
     * In the case of a card, the player has to be in the related game to allow card to be valid.
     * Impossible to add a card for a player that is not in the passed game.
     *
     * @param FormInterface $form
     */
    protected function isFormValid(Form $form)
    {
        if(!$form->isValid()) return false;

        $entity = $form->getData();
        $game = $entity->getGame();
        $player = $entity->getPlayer();
        if($game->getExpectedPlayers()->contains($player)){
            return true;
        } else {
            $form->get('player')->addError(new FormError("card.form.player.incorrect.game"));
        }
        return false;
    }


    /**
     * Retrieves all cards for a given player.
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getPlayerCards($id)
    {
        return $this->repository->getCardsByPlayer($id);
    }

    /**
     * Retrieves all cards for a given player and for a given season.
     *
     * @param $playerID
     * @param $season
     * @return ArrayCollection
     */
    public function getPlayerCardsForSeason($playerID, $season)
    {
        return $this->repository->getCardsByPlayerForSeason($playerID, $season);
    }

    /**
     * Retrieves all cards for a given player and for a given game.
     *
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
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getTeamCards($id)
    {
        return $this->repository->getCardsByTeam($id);
    }

    /**
     * Retrieves all cards for a given team and for a given season.
     *
     * @param $teamID
     * @param $season
     * @return ArrayCollection
     */
    public function getTeamCardsForSeason($teamID, $season)
    {
        return $this->repository->getCardsByTeamForSeason($teamID, $season);
    }

    /**
     * Retrieves all cards for a given team and for a given game.
     *
     * @param $teamID
     * @param $gameID
     * @return ArrayCollection
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