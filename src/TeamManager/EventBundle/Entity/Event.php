<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\ResultBundle\Entity\Comment;
use TeamManager\ResultBundle\Entity\Note;

/**
 * Event
 *
 * @ORM\Entity
 * @ORM\Table(name="tm_event")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="event_type", type="string")
 * @ORM\DiscriminatorMap( {"training"="Training", "game"="Game", "game_friendly"="GameFriendly"} )
 *
 * @ExclusionPolicy("all")
 */
class Event
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    protected $id;

    /**
     * Name of the event.
     *
     * @var string
     * @ORM\Column(name="name", type="string")
     * @Assert\NotBlank(message="form.event.name.blank")
     *
     * @Expose
     */
    protected $name;

    /**
     * Description of the event.
     *
     * @var string
     * @ORM\Column(name="description", type="string", nullable=true)
     *
     * @Expose
     */
    protected $description;

    /**
     * Date of the event.
     *
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotBlank(message="form.event.date.blank")
     *
     * @Expose
     */
    protected $date;

    /**
     * Type of subscription of the event.
     * Implementation of this feature will come in next version with mixed team.
     *
     * @var string
     * @ORM\Column(name="subscription_type", type="string", nullable=true)
     */
    protected $subscription_type;

    /**
     * Limit of players that can be present in the event.
     *
     * @var integer
     * @ORM\Column(name="player_limit", type="integer", nullable=true)
     */
    protected $player_limit;

    /**
     * Name of the opponent.
     *
     * @var string
     * @ORM\Column(name="opponent", type="string", nullable=true)
     * @Assert\NotBlank(message="form.event.opponent.blank", groups={"game", "game_friendly"})
     *
     * @Expose
     */
    protected $opponent;

    /**
     * Location where the event takes place.
     *
     * @var Location
     * @ORM\ManyToOne(targetEntity="\TeamManager\CommonBundle\Entity\Location", cascade="persist")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * @Assert\NotNull(message="form.event.location.blank")
     *
     * @Expose
     */
    protected $location;

    /**
     * Comments sent by users concerning the event.
     *
     * @var Comment
     * @ORM\OneToMany(targetEntity="\TeamManager\ResultBundle\Entity\Comment", mappedBy="event")
     */
    protected $comments;

    /**
     * Notes sent by users concerning the event.
     *
     * @var Comment
     * @ORM\OneToMany(targetEntity="\TeamManager\ResultBundle\Entity\Note", mappedBy="event")
     */
    protected $notes;

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
    protected $expected_players;

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
    protected $missing_players;

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
    protected $present_players;

    /**
     *
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->expected_players = new ArrayCollection();
        $this->missing_players = new ArrayCollection();
        $this->present_players = new ArrayCollection();
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
     * Get event name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set event name.
     *
     * @param string $pName
     * @return Event
     */
    public function setName($pName)
    {
        $this->name = $pName;
        return $this;
    }

    /**
     * Get event description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set event description.
     *
     * @param string $pDescription
     * @return Event
     */
    public function setDescription($pDescription)
    {
        $this->description = $pDescription;
        return $this;
    }

    /**
     * Get event date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set event date.
     *
     * @param \DateTime $pDate
     * @return Event
     */
    public function setDate($pDate)
    {
        $this->date = $pDate;
        return $this;
    }

    /**
     * Get subscription type of the event.
     *
     * @return string
     */
    public function getSubscriptionType()
    {
        return $this->subscription_type;
    }

    /**
     * Set subscription type of the event.
     *
     * @param string $pSubscriptionType
     * @return Event
     */
    public function setSubscriptionType($pSubscriptionType)
    {
        $this->subscription_type = $pSubscriptionType;
        return $this;
    }

    /**
     * Get users limit for the event.
     *
     * @return int
     */
    public function getPlayerLimit()
    {
        return $this->player_limit;
    }

    /**
     * Set users limit for the event.
     *
     * @param int $pLimit
     * @return Event
     */
    public function setPlayerLimit($pLimit)
    {
        $this->player_limit = $pLimit;
        return $this;
    }

    /**
     * Get opponent of the team for this event.
     * Existing only for a game and and friendly game.
     *
     * @return string
     */
    public function getOpponent()
    {
        return $this->opponent;
    }

    /**
     * Set opponent of the team for this event.
     * Existing only for a game and and friendly game.
     *
     * @param string $pOpponent
     * @return Event
     */
    public function setOpponent($pOpponent)
    {
        $this->opponent = $pOpponent;
        return $this;
    }

    /**
     * Get location of the event.
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set location of the event.
     *
     * @param Location $pLocation
     * @return Event
     */
    public function setLocation($pLocation)
    {
        $this->location = $pLocation;
        return $this;
    }

    /**
     * Get comments set for this event.
     *
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add comment to comments list.
     *
     * @param Comment $pComment
     * @return $this
     */
    public function addComment(Comment $pComment)
    {
        $this->comments[] = $pComment;
        return $this;
    }

    /**
     * Removes comment to comments list.
     *
     * @param Comment $pComment
     * @return $this
     */
    public function removeComment(Comment $pComment)
    {
        $this->comments->removeElement($pComment);
        return $this;
    }

    /**
     * Get notes set for the event.
     *
     * @return ArrayCollection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set note in notes list.
     *
     * @param Note $pNote
     * @return $this
     */
    public function addNote(Note $pNote)
    {
        $this->notes[] = $pNote;
        return $this;
    }

    /**
     * Set note in notes list.
     *
     * @param Note $pNote
     * @return $this
     */
    public function removeNote(Note $pNote)
    {
        $this->notes->removeElement($pNote);
        return $this;
    }

    /**
     * Get expected event players list.
     *
     * @return ArrayCollection
     */
    public function getExpectedPlayers()
    {
        return $this->expected_players;
    }

    /**
     * Add player in expected players list.
     *
     * @param Player $pPlayer
     * @return $this
     */
    public function addExpectedPlayer(Player $pPlayer)
    {
        $this->expected_players[] = $pPlayer;
        return $this;
    }

    /**
     * Remove player in expected players list.
     *
     * @param Player $pPlayer
     * @return $this
     */
    public function removeExpectedPlayer(Player $pPlayer)
    {
        $this->expected_players->removeElement($pPlayer);
        return $this;
    }

    /**
     * Get missing event players list.
     *
     * @return ArrayCollection
     */
    public function getMissingPlayers()
    {
        return $this->missing_players;
    }

    /**
     * Add player in missing players list.
     *
     * @param Player $pPlayer
     * @return $this
     */
    public function addMissingPlayer(Player $pPlayer)
    {
        $this->missing_players[] = $pPlayer;
        return $this;
    }

    /**
     * Remove player in missing players list.
     *
     * @param Player $pPlayer
     * @return $this
     */
    public function removeMissingPlayer(Player $pPlayer)
    {
        $this->missing_players->removeElement($pPlayer);
        return $this;
    }

    /**
     * Get present event players list.
     *
     * @return ArrayCollection
     */
    public function getPresentPlayers()
    {
        return $this->present_players;
    }

    /**
     * Add player in present players list.
     *
     * @param Player $pPlayer
     * @return $this
     */
    public function addPresentPlayer(Player $pPlayer)
    {
        $this->present_players[] = $pPlayer;
        return $this;
    }

    /**
     * Remove player in present players list.
     *
     * @param Player $pPlayer
     * @return $this
     */
    public function removePresentPlayer(Player $pPlayer)
    {
        $this->present_players->removeElement($pPlayer);
        return $this;
    }

}
