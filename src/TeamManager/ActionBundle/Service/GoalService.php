<?php

namespace TeamManager\ActionBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormInterface;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\CommonBundle\Service\EntityRestService;

class GoalService extends EntityRestService
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
     * In the case of a goal, the player has to be in the related game to allow goal to be valid.
     * Impossible to add a goal for a player that is not in the passed game.
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
            $form->get('player')->addError(new FormError("goal.form.player.incorrect.game"));
        }
        return false;
    }

    /**
     * Retrieves all goals for a given player.
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getPlayerGoals($id)
    {
        return $this->repository->getGoalsByPlayer($id);
    }

    /**
     * Retrieves all goals for a given player and for a given season.
     *
     * @param $playerID
     * @param $season
     * @return ArrayCollection
     */
    public function getPlayerGoalsForSeason($playerID, $season)
    {
        return $this->repository->getGoalsByPlayerForSeason($playerID, $season);
    }

    /**
     * Retrieves all goals for a given player and for a given game.
     *
     * @param $playerID
     * @param $gameID
     * @return ArrayCollection
     */
    public function getPlayerGoalsForGame($playerID, $gameID)
    {
        return $this->repository->getGoalsByPlayerForGame($playerID, $gameID);
    }

    /**
     * Retrieves all goals for a given team.
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getTeamGoals($id)
    {
        return $this->repository->getGoalsByTeam($id);
    }

    /**
     * Retrieves all goals for a given team and for a given season.
     *
     * @param $teamID
     * @param $season
     * @return ArrayCollection
     */
    public function getTeamGoalsForSeason($teamID, $season)
    {
        return $this->repository->getGoalsByTeamForSeason($teamID, $season);
    }

    /**
     * Retrieves all goals for a given team and for a given game.
     *
     * @param $teamID
     * @param $gameID
     * @return ArrayCollection
     */
    public function getTeamGoalsForGame($teamID, $gameID)
    {
        return $this->repository->getGoalsByTeamForGame($teamID, $gameID);
    }

    /**
     * Retrieves all goals for a given game.
     */
    public function getGameGoals($id)
    {
        return $this->repository->getGoalsByGame($id);
    }

}