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
 * Comment
 *
 * @ORM\Table(name="tm_comment")
 * @ORM\Entity(repositoryClass="TeamManager\ResultBundle\Repository\CommentRepository")
 *
 * @ExclusionPolicy("all")
 */
class Comment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     * @Groups({"CommentSender", "CommentReceiver", "CommentGame", "CommentSpecific"})
     */
    private $id;

    /**
     * Content of the comment.
     *
     * @var string
     * @ORM\Column(name="content", type="string")
     *
     * @Assert\NotBlank(message="form.comment.content.blank")
     *
     * @Expose
     * @Groups({"CommentSender", "CommentReceiver", "CommentGame", "CommentSpecific"})
     */
    private $content;

    /**
     * Game for which the comment has been left
     *
     * @var Game
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Event", inversedBy="comments")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotNull(message="form.comment.event.null")
     *
     * @Expose
     * @Groups({"CommentSender", "CommentReceiver", "CommentSpecific"})
     */
    private $event;

    /**
     * Player who received the comment.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="comments_received")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotNull(message="form.comment.receiver.null")
     *
     * @Expose
     * @Groups({"CommentSender", "CommentGame", "CommentSpecific"})
     */
    private $player_receiver;

    /**
     * Player who sent the comment.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="comments_sent")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     *
     * @Assert\NotNull(message="form.comment.sender.null")
     *
     * @Expose
     * @Groups({"CommentReceiver", "CommentGame", "CommentSpecific"})
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
     * Set comment content.
     *
     * @param string $pContent
     * @return Comment
     */
    public function setContent($pContent)
    {
        $this->content = $pContent;
        return $this;
    }

    /**
     * Get comment content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get comment related event.
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
     * Get comment related event.
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
