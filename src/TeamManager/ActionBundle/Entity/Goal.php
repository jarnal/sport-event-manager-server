<?php

namespace TeamManager\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Goal
 *
 * @ORM\Table(name="tm_goal")
 * @ORM\Entity(repositoryClass="TeamManager\ActionBundle\Repository\GoalRepository")
 *
 * @ExclusionPolicy("all")
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
     *
     * @Expose
     * @Groups({"GoalTeam", "GoalPlayer", "GoalGame", "GoalSpecific"})
     */
    private $id;

    /**
     * Goal type. Can be: AUTOGOAL, HEADER, VOLLEY, NORMAL, SPECIAL.
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     *
     * @Assert\NotBlank(message="form.goal.type.blank")
     *
     * @Expose
     * @Groups({"GoalTeam", "GoalPlayer", "GoalGame", "GoalSpecific"})
     */
    private $type;

    /**
     * Time when the goal has been scored.
     *
     * @var \DateTime
     * @ORM\Column(name="time", type="datetime", nullable=true)
     *
     * @Expose
     * @Groups({"GoalSpecific"})
     */
    private $time;

    /**
     * Player who scored the goal.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="goals")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotNull(message="form.goal.player.null")
     *
     * @Expose
     * @Groups({"GoalTeam", "GoalGame", "GoalSpecific"})
     */
    private $player;

    /**
     * Game in which the goal has been scored.
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Game", inversedBy="goals")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotNull(message="form.goal.game.null")
     *
     * @Expose
     * @Groups({"GoalTeam", "GoalPlayer", "GoalSpecific"})
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
     * @return Goal
     */
    public function setType($pType)
    {
        $this->type = $pType;
        return $this;
    }

    /**
     * Get goal type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get goal time.
     *
     * @param \DateTime $pTime
     * @return Goal
     */
    public function setTime($pTime)
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
     * @return Goal
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
     * @return Goal
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
