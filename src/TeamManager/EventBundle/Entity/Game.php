<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
use TeamManager\ActionBundle\Entity\Card;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\ActionBundle\Entity\Injury;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Game
 *
 * @ORM\Entity(repositoryClass="TeamManager\EventBundle\Repository\GameRepository")
 * @ORM\Table(name="tm_game")
 *
 * @ExclusionPolicy("all")
 */
class Game extends Event
{

    /**
     * Defines if the game is friendly or not.
     *
     * @var boolean
     * @ORM\Column(name="friendly", type="boolean")
     */
    private $friendly = false;

    /**
     * Name of the opponent.
     *
     * @var string
     * @ORM\Column(name="opponent", type="string", nullable=true)
     * @Assert\NotBlank(message="form.event.opponent.blank")
     *
     * @Groups({"EventMinimal", "EventPlayer", "EventTeam", "EventDetails"})
     * @Expose
     */
    protected $opponent;

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
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->goals = new ArrayCollection();
        $this->cards = new ArrayCollection();
        $this->injuries = new ArrayCollection();
    }

    /**
     * Get opponent of the team for this event.
     * Existing only for a game and and friendly game.
     *
     * @return string
     */
    public function getOpponent()
    {
        return $this->opponent;
    }

    /**
     * Set opponent of the team for this event.
     * Existing only for a game and and friendly game.
     *
     * @param string $pOpponent
     * @return Event
     */
    public function setOpponent($pOpponent)
    {
        $this->opponent = $pOpponent;
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

    /**
     * Get if game is friendly or not.
     *
     * @return boolean
     */
    public function isFriendly()
    {
        return $this->friendly;
    }

    /**
     * Set if game is friendly or not.
     *
     * @param boolean $friendly
     */
    public function setFriendly($friendly)
    {
        $this->friendly = $friendly;
    }

}
