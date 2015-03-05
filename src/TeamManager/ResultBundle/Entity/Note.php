<?php

namespace TeamManager\ResultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\Entity\Player;

/**
 * Note
 *
 * @ORM\Table(name="tm_note")
 * @ORM\Entity(repositoryClass="TeamManager\ResultBundle\Repository\NoteRepository")
 */
class Note
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
     * Content of the note.
     *
     * @var integer
     * @ORM\Column(name="content", type="integer")
     */
    private $content;

    /**
     * Game for which the note has been left
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Event", inversedBy="notes")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * Player who received the note.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="notes_received")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $player_receiver;

    /**
     * Player who sent the note.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="notes_sent")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $player_sender;


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
     * Get card related game.
     *
     * @param integer $pContent
     * @return Comment
     */
    public function setContent($pContent)
    {
        $this->content = $pContent;
        return $this;
    }

    /**
     * Get card related game.
     *
     * @return integer
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get card related game.
     *
     * @param Game $pGame
     * @return Comment
     */
    public function setEvent(Game $pGame)
    {
        $this->event = $pGame;
        return $this;
    }

    /**
     * Get card related game.
     *
     * @return Game
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return Player
     */
    public function getPlayerReceiver()
    {
        return $this->player_receiver;
    }

    /**
     * @param Player $pPlayerReceiver
     * @return Comment
     */
    public function setPlayerReceiver(Player $pPlayerReceiver)
    {
        $this->player_receiver = $pPlayerReceiver;
        return $this;
    }

    /**
     * @return Player
     */
    public function getPlayerSender()
    {
        return $this->player_sender;
    }

    /**
     * @param Player $pPlayerSender
     * @return Comment
     */
    public function setPlayerSender(Player $pPlayerSender)
    {
        $this->player_sender = $pPlayerSender;
        return $this;
    }
}
