<?php

namespace TeamManager\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Card
 *
 * @ORM\Table(name="tm_card")
 * @ORM\Entity(repositoryClass="TeamManager\ActionBundle\Repository\CardRepository")
 */
class Card
{
    const YELLOW_CARD = "Card.YELLOW_CARD";
    const RED_CARD = "Card.RED_CARD";

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Type of card, can be YELLOW_CARD or RED_CARD.
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * Time when the card has been received.
     *
     * @var \DateTime
     * @ORM\Column(name="time", type="datetime", nullable=true)
     */
    private $time;

    /**
     * Player who received the card.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="cards")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;

    /**
     * Game in which the card has been received.
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Game", inversedBy="cards")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * Team in which the player was playing when card has been received.
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
     * Set card type.
     *
     * @param string $pTime
     * @return Card
     */
    public function setType($pType)
    {
        $this->type = $pType;
        return $this;
    }

    /**
     * Get card type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get card time.
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
     * Get card time.
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set card related player.
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
     * Get card related player.
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get card related game.
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
     * Get card related game.
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

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

}
