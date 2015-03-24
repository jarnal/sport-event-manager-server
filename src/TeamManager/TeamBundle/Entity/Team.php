<?php

namespace TeamManager\TeamBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\EventBundle\Entity\Event;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\EventBundle\Entity\GameFriendly;
use TeamManager\EventBundle\Entity\Training;
use TeamManager\PlayerBundle\Entity\Player;

/**
 * Team
 *
 * @ORM\Table(name="tm_team")
 * @ORM\Entity(repositoryClass="TeamManager\TeamBundle\Repository\TeamRepository")
 *
 * @ExclusionPolicy("all")
 */
class Team
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     * @Groups({"TeamGlobal", "TeamSpecific"})
     */
    private $id;

    /**
     * Name of the team.
     *
     * @var string
     * @ORM\Column(name="name", type="string")
     * @Assert\NotBlank(message="form.team.name.blank")
     *
     * @Expose
     * @Groups({"TeamGlobal", "TeamSpecific"})
     */
    private $name;

    /**
     * Description of the team.
     *
     * @var string
     * @ORM\Column(name="description", type="string", nullable=true)
     *
     * @Expose
     * @Groups({"TeamSpecific"})
     */
    private $description;

    /**
     * Path to the team image uploaded.
     *
     * @var string
     * @ORM\Column(name="image_url", type="string", nullable=true)
     *
     * @Expose
     * @Groups({"TeamGlobal", "TeamSpecific"})
     */
    private $image_url;

    /**
     * Default location where de team plays and trains.
     *
     * @var Location
     * @ORM\ManyToOne(targetEntity="\TeamManager\CommonBundle\Entity\Location", cascade="persist")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * @Assert\NotNull(message="form.team.location.null")
     *
     * @Expose
     * @Groups({"TeamSpecific"})
     */
    private $default_location;

    /**
     * Team manager.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="managed_teams")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id")
     * @Assert\NotNull(message="form.team.manager.null")
     *
     * @Expose
     * @Groups({"TeamSpecific"})
     */
    private $manager;

    /**
     * List of team players.
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\TeamManager\PlayerBundle\Entity\Player", cascade="persist", mappedBy="teams")
     *
     * @Expose
     * @Groups({"TeamSpecific"})
     */
    private $players;

    /**
     * Trainings of the team.
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\TeamManager\EventBundle\Entity\Event", mappedBy="team")
     */
    private $events;

    /**
     *
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->events = new ArrayCollection();
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
     * Get team name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set team name.
     *
     * @param string $pName
     * @return Team
     */
    public function setName($pName)
    {
        $this->name = $pName;
        return $this;
    }

    /**
     * Get team description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set team description.
     *
     * @param string $pDescription
     * @return Team
     */
    public function setDescription($pDescription)
    {
        $this->description = $pDescription;
        return $this;
    }

    /**
     * Get team image url.
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Set team image url.
     *
     * @param string $pImageUrl
     * @return Team
     */
    public function setImageUrl($pImageUrl)
    {
        $this->image_url = $pImageUrl;
        return $this;
    }

    /**
     * Get team default location.
     *
     * @return Location
     */
    public function getDefaultLocation()
    {
        return $this->default_location;
    }

    /**
     * Set team default location
     *
     * @param Location $pDefaultLocation
     * @return Team
     */
    public function setDefaultLocation($pDefaultLocation)
    {
        $this->default_location = $pDefaultLocation;
        return $this;
    }

    /**
     * Set team manager.
     *
     * @return Player
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Get team manager.
     *
     * @param Player $pManager
     * @return Team
     */
    public function setManager($pManager)
    {
        $this->manager = $pManager;
        return $this;
    }

    /**
     * Get team players list.
     *
     * @return ArrayCollection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Add player in team players list.
     *
     * @param Player $pPlayer
     * @return Team
     */
    public function addPlayer(Player $pPlayer)
    {
        $this->players[] = $pPlayer;
        $pPlayer->addTeam($this);
        return $this;
    }

    /**
     * Remove player from team players list.
     *
     * @param Player $pPlayer
     * @return Team
     */
    public function removePlayer(Player $pPlayer)
    {
        $this->players->removeElement($pPlayer);
        $pPlayer->removeTeam($this);
        return $this;
    }

    /**
     * Get team trainings list.
     *
     * @return ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add training in team trainings list.
     *
     * @param Event $pEvent
     * @return Team
     */
    public function addEvent(Event $pEvent)
    {
        $this->events[] = $pEvent;
        return $this;
    }

    /**
     * Remove training from team trainings list.
     *
     * @param Event $pEvent
     * @return Team
     */
    public function removeEvent(Event $pEvent)
    {
        $this->events->removeElement($pEvent);
        return $this;
    }

    /**
     * Get team games list.
     *
     * @return ArrayCollection
     *
     * @VirtualProperty
     * @SerializedName( "games" )
     * @Groups( {"TeamSpecific"} )
     */
    public function getGames()
    {
        $games = $this->events;
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq("type", "game"));
        $match = $games->matching($criteria);

        return $match;
    }

    /**
     * Get team friendly games list.
     *
     * @return ArrayCollection
     *
     * @VirtualProperty
     * @SerializedName( "friendly_games" )
     * @Groups( {"TeamSpecific"} )
     */
    public function getFriendlyGames()
    {
        $games = $this->events;
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq("type", "game_friendly"));
        $match = $games->matching($criteria);

        return $match;
    }

    /**
     * Get team training list.
     *
     * @return ArrayCollection
     *
     * @VirtualProperty
     * @SerializedName( "trainings" )
     * @Groups( {"TeamSpecific"} )
     */
    public function getTrainings()
    {
        $training = $this->events;
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq("type", "training"));
        $match = $training->matching($criteria);

        return $match;
    }

}