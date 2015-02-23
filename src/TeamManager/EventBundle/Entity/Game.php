<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TeamManager\ActionBundle\Entity\Card;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\ActionBundle\Entity\Injury;
use TeamManager\ResultBundle\Entity\Comment;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Game
 *
 * @ORM\Entity
 */
class Game extends Event
{

    /**
     * Team participating to the game.
     *
     * @var Team
     * @ORM\ManyToOne(targetEntity="TeamManager\TeamBundle\Entity\Team", inversedBy="games")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Goal", cascade="persist", mappedBy="game")
     */
    private $goals;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Card", cascade="persist", mappedBy="game")
     */
    private $cards;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ResultBundle\Entity\Comment", cascade="persist", mappedBy="game")
     */
    private $comments;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Injury", cascade="persist", mappedBy="game")
     */
    private $injuries;

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $pTeam
     * @return Game
     */
    public function setTeam(Team $pTeam)
    {
        $this->team = $pTeam;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Add a goal.
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
     * Remove a goal.
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
     * @return ArrayCollection
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Add a card.
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
     * Remove a card.
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
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add a comment.
     *
     * @param \TeamManager\ResultBundle\Entity\Comment $pComment
     * @return Game
     */
    public function addComment(Comment $pComment)
    {
        $this->comments[] = $pComment;
        return $this;
    }

    /**
     * Remove a comment.
     *
     * @param Comment $pComment
     * @return Game
     */
    public function removeComment(Comment $pComment)
    {
        $this->comments->removeElement($pComment);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getInjuries()
    {
        return $this->injuries;
    }

    /**
     * Add a comment.
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
     * Remove a comment.
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
