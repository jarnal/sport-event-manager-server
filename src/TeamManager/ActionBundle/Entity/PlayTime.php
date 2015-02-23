<?php

namespace TeamManager\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\TeamBundle\Entity\Team;

/**
 * PlayTime
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TeamManager\ActionBundle\Repository\PlayTimeRepository")
 */
class PlayTime
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Duration in minutes the player played during the game.
     *
     * @var integer
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /**
     * Player related to the play time.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="play_times")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;

    /**
     * Game related to the play time.
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Game")
     */
    private $game;

    /**
     * Team in which the player was playing when playing.
     *
     * @var Team
     * @ORM\ManyToOne(targetEntity="TeamManager\TeamBundle\Entity\Team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
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
     * Set play time duration.
     *
     * @param integer $pDuration
     * @return PlayTime
     */
    public function setDuration($pDuration)
    {
        $this->duration = $pDuration;
        return $this;
    }

    /**
     * Get play time duration.
     *
     * @return Player
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set play time related player.
     *
     * @param Player $pPlayer
     * @return PlayTime
     */
    public function setPlayer(Player $pPlayer)
    {
        $this->player = $pPlayer;
        return $this;
    }

    /**
     * Get play time related player.
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get play time related game.
     *
     * @param Game $pGame
     * @return PlayTime
     */
    public function setGame(Game $pGame)
    {
        $this->game = $pGame;
        return $this;
    }

    /**
     * Get play time related game.
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param Team $pTeam
     * @return PlayTime
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
