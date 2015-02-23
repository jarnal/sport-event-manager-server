<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\ResultBundle\Entity\Comment;
use TeamManager\ResultBundle\Entity\Note;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Event
 *
 * @ORM\Entity
 * @ORM\Table(name="tm_event")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="event_type", type="string")
 * @ORM\DiscriminatorMap( {"training"="Training", "game"="Game", "game_friendly"="GameFriendly"} )
 */
abstract class Event
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
     * Name of the event.
     *
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * Description of the event.
     *
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * Date of the event.
     *
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * Type of subscription of the event.
     * Implementation of this feature will come in next version with mixed team.
     *
     * @var string
     * @ORM\Column(name="subscription_type", type="string")
     */
    private $subscription_type;

    /**
     * Limit of players that can be present in the event.
     *
     * @var integer
     * @ORM\Column(name="limit", type="integer")
     */
    private $limit;

    /**
     * Name of the opponent.
     *
     * @var string
     * @ORM\Column(name="opponent", type="integer")
     */
    private $opponent;

    /**
     * Location where the event takes place.
     *
     * @var Location
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    private $location;

    /**
     * Comments sent by users concerning the event.
     *
     * @var Comment
     * @ORM\OneToMany(targetEntity="\TeamManager\ResultBundle\Entity\Comment", mappedBy="team")
     */
    private $comments;

    /**
     * Notes sent by users concerning the event.
     *
     * @var Comment
     * @ORM\OneToMany(targetEntity="\TeamManager\ResultBundle\Entity\Note", mappedBy="team")
     */
    private $notes;

    /**
     * Players expected to play during this event.
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\TeamManager\PlayerBundle\Entity\Player")
     * @ORM\JoinTable(name="tm_event_expected_player",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $expected_players;

    /**
     * Players that will be missing for the event (among those expected).
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\TeamManager\PlayerBundle\Entity\Player")
     * @ORM\JoinTable(name="tm_event_missing_player",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $missing_players;

    /**
     * Players that will be present for the event (among those expected).
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\TeamManager\PlayerBundle\Entity\Player")
     * @ORM\JoinTable(name="tm_event_present_player",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $present_players;

    /**
     *
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->expected_players = new ArrayCollection();
        $this->$missing_players = new ArrayCollection();
        $this->$present_players = new ArrayCollection();
    }

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $pName
     * @return Event
     */
    public function setName($pName)
    {
        $this->name = $pName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $pDescription
     * @return Event
     */
    public function setDescription($pDescription)
    {
        $this->description = $pDescription;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $pDate
     * @return Event
     */
    public function setDate($pDate)
    {
        $this->date = $pDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubscriptionType()
    {
        return $this->subscription_type;
    }

    /**
     * @param string $pSubscriptionType
     * @return Event
     */
    public function setSubscriptionType($pSubscriptionType)
    {
        $this->subscription_type = $pSubscriptionType;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $pLimit
     * @return Event
     */
    public function setLimit($pLimit)
    {
        $this->limit = $pLimit;
        return $this;
    }

    /**
     * @return string
     */
    public function getOpponent()
    {
        return $this->opponent;
    }

    /**
     * @param string $pOpponent
     * @return Event
     */
    public function setOpponent($pOpponent)
    {
        $this->opponent = $pOpponent;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $pLocation
     * @return Event
     */
    public function setLocation($pLocation)
    {
        $this->location = $pLocation;
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
     * @param Comment $pComment
     * @return $this
     */
    public function addComment(Comment $pComment)
    {
        $this->comments[] = $pComment;
        return $this;
    }

    /**
     * @param Comment $pComment
     * @return $this
     */
    public function removeComment(Comment $pComment)
    {
        $this->comments->removeElement($pComment);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param Note $pNote
     * @return $this
     */
    public function addNote(Note $pNote)
    {
        $this->notes[] = $pNote;
        return $this;
    }

    /**
     * @param Note $pNote
     * @return $this
     */
    public function removeNote(Note $pNote)
    {
        $this->notes->removeElement($pNote);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getExpectedPlayers()
    {
        return $this->expected_players;
    }

    /**
     * @param Player $pPlayer
     * @return $this
     */
    public function addExpectedPlayer(Player $pPlayer)
    {
        $this->expected_players[] = $pPlayer;
        return $this;
    }

    /**
     * @param Player $pPlayer
     * @return $this
     */
    public function removeExpectedPlayer(Player $pPlayer)
    {
        $this->expected_players->removeElement($pPlayer);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMissingPlayers()
    {
        return $this->missing_players;
    }

    /**
     * @param Player $pPlayer
     * @return $this
     */
    public function addMissingPlayer(Player $pPlayer)
    {
        $this->missing_players[] = $pPlayer;
        return $this;
    }

    /**
     * @param Player $pPlayer
     * @return $this
     */
    public function removeMissingPlayer(Player $pPlayer)
    {
        $this->missing_players->removeElement($pPlayer);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPresentPlayers()
    {
        return $this->present_players;
    }

    /**
     * @param Player $pPlayer
     * @return $this
     */
    public function addPresentPlayer(Player $pPlayer)
    {
        $this->present_players[] = $pPlayer;
        return $this;
    }

    /**
     * @param Player $pPlayer
     * @return $this
     */
    public function removePresentPlayer(Player $pPlayer)
    {
        $this->present_players->removeElement($pPlayer);
        return $this;
    }

}
