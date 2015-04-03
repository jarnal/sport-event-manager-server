<?php

namespace TeamManager\ResultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
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
     *
     * @Expose
     * @Groups({"NoteSender", "NoteReceiver", "NoteGame", "NoteSpecific"})
     */
    private $id;

    /**
     * Content of the note.
     *
     * @var integer
     * @ORM\Column(name="content", type="integer")
     *
     * @Assert\GreaterThan(message="form.note.content.invalid", value=-1)
     * @Assert\LessThan(message="form.note.content.invalid", value=21)
     *
     * @Expose
     * @Groups({"NoteSender", "NoteReceiver", "NoteGame", "NoteSpecific"})
     */
    private $content;

    /**
     * Game for which the note has been left
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Event", inversedBy="notes")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotNull(message="form.note.event.null")
     *
     * @Expose
     * @Groups({"NoteSender", "NoteReceiver", "NoteSpecific"})
     */
    private $event;

    /**
     * Player who received the note.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="notes_received")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotNull(message="form.note.receiver.null")
     *
     * @Expose
     * @Groups({"NoteSender", "NoteGame", "NoteSpecific"})
     */
    private $player_receiver;

    /**
     * Player who sent the note.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="notes_sent")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     *
     * @Assert\NotNull(message="form.note.sender.null")
     *
     * @Expose
     * @Groups({"NoteReceiver", "NoteGame", "NoteSpecific"})
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
     * Get note related game.
     *
     * @param integer $pContent
     * @return Note
     */
    public function setContent($pContent)
    {
        $this->content = $pContent;
        return $this;
    }

    /**
     * Get note related game.
     *
     * @return integer
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get note related event.
     *
     * @param Game $pGame
     * @return Note
     */
    public function setEvent(Game $pGame)
    {
        $this->event = $pGame;
        return $this;
    }

    /**
     * Get note related event.
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
     * @return Note
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
     * @return Note
     */
    public function setPlayerSender(Player $pPlayerSender)
    {
        $this->player_sender = $pPlayerSender;
        return $this;
    }
}
