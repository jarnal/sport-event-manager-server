<?php

namespace TeamManager\ActionBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class InjuryService extends EntityRestService
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
            $form->get('player')->addError(new FormError("injury.form.player.incorrect.game"));
        }
        return false;
    }

    /**
     * Retrieves all injuries for a given player.
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getPlayerInjuries($id)
    {
        return $this->repository->getInjuriesByPlayer($id);
    }

    /**
     * Retrieves all injuries for a given player and for a given season.
     *
     * @param $playerID
     * @param $season
     * @return ArrayCollection
     */
    public function getPlayerInjuriesForSeason($playerID, $season)
    {
        return $this->repository->getInjuriesByPlayerForSeason($playerID, $season);
    }

    /**
     * Retrieves all injuries for a given player and for a given game.
     *
     * @param $playerID
     * @param $gameID
     * @return ArrayCollection
     */
    public function getPlayerInjuriesForGame($playerID, $gameID)
    {
        return $this->repository->getInjuriesByPlayerForGame($playerID, $gameID);
    }

    /**
     * Retrieves all injuries for a given team.
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getTeamInjuries($id)
    {
        return $this->repository->getInjuriesByTeam($id);
    }

    /**
     * Retrieves all injuries for a given team and for a given season.
     *
     * @param $teamID
     * @param $season
     * @return ArrayCollection
     */
    public function getTeamInjuriesForSeason($teamID, $season)
    {
        return $this->repository->getInjuriesByTeamForSeason($teamID, $season);
    }

    /**
     * Retrieves all injuries for a given team and for a given game.
     *
     * @param $teamID
     * @param $gameID
     * @return ArrayCollection
     */
    public function getTeamInjuriesForGame($teamID, $gameID)
    {
        return $this->repository->getInjuriesByTeamForGame($teamID, $gameID);
    }

    /**
     * Retrieves all injuries for a given game.
     */
    public function getGameInjuries($id)
    {
        return $this->repository->getInjuriesByGame($id);
    }

}