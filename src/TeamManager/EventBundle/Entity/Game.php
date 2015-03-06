<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TeamManager\ActionBundle\Entity\Card;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\ActionBundle\Entity\Injury;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Game
 *
 * @ORM\Entity
 * @ORM\Table(name="tm_game")
 */
class Game extends Event
{

    /**
     * Team participating to the game.
     *
     * @var Team
     * @ORM\ManyToOne(targetEntity="TeamManager\TeamBundle\Entity\Team", inversedBy="games")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotNull(message="form.game.team.blank")
     */
    private $team;

    /**
     * Goals scored during the game.
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Goal", cascade="persist", mappedBy="game")
     */
    private $goals;

    /**
     * Card set during the game.
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Card", cascade="persist", mappedBy="game")
     */
    private $cards;

    /**
     * Injuries occurred during the game.
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Injury", cascade="persist", mappedBy="game")
     */
    private $injuries;

    /**
     * Get team participating to the game.
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set the team participating to the game.
     *
     * @param Team $pTeam
     * @return Game
     */
    public function setTeam(Team $pTeam)
    {
        $this->team = $pTeam;
        return $this;
    }

    /**
     * Get goal scored.
     *
     * @return ArrayCollection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Add a goal to goals list.
     *
     * @param Goal $pGoal
     * @return Game
     */
    public function addGoal(Goal $pGoal)
    {
        $this->goals[] = $pGoal;
        return $this;
    }

    /**
     * Remove a goal from goals list.
     *
     * @param Goal $pGoal
     * @return Game
     */
    public function removeGoal(Goal $pGoal)
    {
        $this->goals->removeElement($pGoal);
        return $this;
    }

    /**
     * Get cards set during the game.
     *
     * @return ArrayCollection
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Add a card in cards list.
     *
     * @param Card $pCard
     * @return Game
     */
    public function addCard(Card $pCard)
    {
        $this->cards[] = $pCard;
        return $this;
    }

    /**
     * Remove a card from cards list.
     *
     * @param Card $pCard
     * @return Game
     */
    public function removeCard(Card $pCard)
    {
        $this->cards->removeElement($pCard);
        return $this;
    }

    /**
     * Get injuries occurred during the game.
     *
     * @return ArrayCollection
     */
    public function getInjuries()
    {
        return $this->injuries;
    }

    /**
     * Add an injury from injuries list.
     *
     * @param Injury $pInjury
     * @return Game
     */
    public function addInjuries(Injury $pInjury)
    {
        $this->injuries[] = $pInjury;
        return $this;
    }

    /**
     * Remove an injury from injuries list
     *
     * @param Injury $pInjury
     * @return Game
     */
    public function removeInjuries(Injury $pInjury)
    {
        $this->injuries->removeElement($pInjury);
        return $this;
    }

}
