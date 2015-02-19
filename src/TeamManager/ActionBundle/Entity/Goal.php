<?php

namespace TeamManager\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\Entity\Player;

/**
 * Goal
 *
 * @ORM\Table(name="tm_goal")
 * @ORM\Entity(repositoryClass="TeamManager\ActionBundle\Repository\GoalRepository")
 */
class Goal
{
    const AUTOGOAL = "Goal.AUTOGOAL";
    const HEADER = "Goal.HEADER";
    const VOLLEY = "Goal.VOLLEY";
    const NORMAL = "Goal.NORMAL";
    const SPECIAL = "Goal.SPECIAL";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Goal type. Can be : AUTOGOAL, HEADER, VOLLEY, NORMAL, SPECIAL.
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * Time when the goal has been scored.
     *
     * @var \DateTime
     * @ORM\Column(name="time", type="datetime", nullable=true)
     */
    private $time;

    /**
     * Player who scored the goal.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player")
     */
    private $player;

    /**
     * Game in which the goal has been scored.
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Game")
     */
    private $game;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set goal type.
     *
     * @param string $pTime
     * @return Card
     */
    public function getType($pType)
    {
        $this->type = $pType;
        return $this;
    }

    /**
     * Get goal type.
     *
     * @return string
     */
    public function setType()
    {
        return $this->type;
    }

    /**
     * Get goal time.
     *
     * @param \DateTime $pTime
     * @return Card
     */
    public function setTime(\DateTime $pTime)
    {
        $this->time = $pTime;
        return $this;
    }

    /**
     * Get goal time.
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set goal related player.
     *
     * @param Player $pPlayer
     * @return Card
     */
    public function setPlayer(Player $pPlayer)
    {
        $this->player = $pPlayer;
        return $this;
    }

    /**
     * Get goal related player.
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get goal related game.
     *
     * @param Game $pGame
     * @return Card
     */
    public function setGame(Game $pGame)
    {
        $this->game = $pGame;
        return $this;
    }

    /**
     * Get goal related game.
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }
}
