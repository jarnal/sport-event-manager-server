<?php

namespace TeamManager\ActionBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class PlayTimeService extends EntityRestService
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
     * In the case of a card, the player has to be in the related game to allow card to be valid.
     * Impossible to add a card for a player that is not in the passed game.
     *
     * @param Form $form
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
            $form->get('player')->addError(new FormError("play_time.form.player.incorrect.game"));
        }
        return false;
    }

    /**
     * Retrieves all cards for a given player.
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getPlayerPlayTimes($id)
    {
        return $this->repository->getPlayTimesByPlayer($id);
    }

    /**
     * Retrieves all cards for a given player and for a given season.
     *
     * @param $playerID
     * @param $season
     * @return ArrayCollection
     */
    public function getPlayerPlayTimesForSeason($playerID, $season)
    {
        return $this->repository->getPlayTimesByPlayerForSeason($playerID, $season);
    }

    /**
     * Retrieves all cards for a given player and for a given game.
     *
     * @param $playerID
     * @param $gameID
     * @return ArrayCollection
     */
    public function getPlayerPlayTimesForGame($playerID, $gameID)
    {
        return $this->repository->getPlayTimesByPlayerForGame($playerID, $gameID);
    }

    /**
     * Retrieves all cards for a given team.
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getTeamPlayTimes($id)
    {
        return $this->repository->getPlayTimesByTeam($id);
    }

    /**
     * Retrieves all cards for a given team and for a given season.
     *
     * @param $teamID
     * @param $season
     * @return ArrayCollection
     */
    public function getTeamPlayTimesForSeason($teamID, $season)
    {
        return $this->repository->getPlayTimesByTeamForSeason($teamID, $season);
    }

    /**
     * Retrieves all cards for a given team and for a given game.
     *
     * @param $teamID
     * @param $gameID
     * @return ArrayCollection
     */
    public function getTeamPlayTimesForGame($teamID, $gameID)
    {
        return $this->repository->getPlayTimesByTeamForGame($teamID, $gameID);
    }

    /**
     * Retrieves all cards for a given game.
     */
    public function getGamePlayTimes($id)
    {
        return $this->repository->getPlayTimesByGame($id);
    }

}