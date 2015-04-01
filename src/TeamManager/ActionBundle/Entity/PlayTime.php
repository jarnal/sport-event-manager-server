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
 * PlayTime
 *
 * @ORM\Table(name="tm_play_time")
 * @ORM\Entity(repositoryClass="TeamManager\ActionBundle\Repository\PlayTimeRepository")
 *
 * @ExclusionPolicy("all")
 */
class PlayTime
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     * @Groups({"PlayTimeTeam", "PlayTimePlayer", "PlayTimeGame", "PlayTimeSpecific"})
     */
    private $id;

    /**
     * Duration in minutes the player played during the game.
     *
     * @var integer
     * @ORM\Column(name="duration", type="integer")
     *
     * @Assert\NotBlank(message="form.play_time.duration.blank")
     *
     * @Expose
     * @Groups({"PlayTimeTeam", "PlayTimePlayer", "PlayTimeGame", "PlayTimeSpecific"})
     */
    private $duration;

    /**
     * Player related to the play time.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="play_times")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotNull(message="form.play_time.player.null")
     *
     * @Expose
     * @Groups({"PlayTimeTeam", "PlayTimeGame", "PlayTimeSpecific"})
     */
    private $player;

    /**
     * Game related to the play time.
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Game")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotNull(message="form.play_time.game.null")
     *
     * @Expose
     * @Groups({"PlayTimeTeam", "PlayTimePlayer", "PlayTimeSpecific"})
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
     * Set play time duration (minutes).
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
}
