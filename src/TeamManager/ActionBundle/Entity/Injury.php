<?php

namespace TeamManager\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Injury
 *
 * @ORM\Table(name="tm_injury")
 * @ORM\Entity(repositoryClass="TeamManager\ActionBundle\Repository\InjuryRepository")
 */
class Injury
{
    const LIGHT = "Injury.LIGHT";
    const NORMAL = "Injury.NORMAL";
    const SERIOUS = "Injury.SERIOUS";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Injury type. Can be : LIGHT, NORMAL, SERIOUS.
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * Player who is injured.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="injuries")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $player;

    /**
     * Game in which the player has been injured.
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Game", inversedBy="injuries")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $game;

    /**
     * Team in which the player was playing when injured.
     *
     * @var Team
     * @ORM\ManyToOne(targetEntity="TeamManager\TeamBundle\Entity\Team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $team;


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
     * @return Injury
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
     * Set goal related player.
     *
     * @param Player $pPlayer
     * @return Injury
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
     * @return Injury
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

    /**
     * @param Team $pTeam
     * @return Injury
     */
    public function setTeam(Team $pTeam)
    {
        $this->team = $pTeam;
        return $this;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
