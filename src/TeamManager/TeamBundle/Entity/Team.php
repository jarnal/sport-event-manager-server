<?php

namespace TeamManager\TeamBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\EventBundle\Entity\GameFriendly;
use TeamManager\EventBundle\Entity\Training;
use TeamManager\EventBundle\Entity\Location;
use TeamManager\PlayerBundle\Entity\Player;

/**
 * Team
 *
 * @ORM\Table(name="tm_team")
 * @ORM\Entity(repositoryClass="TeamManager\TeamBundle\Repository\TeamRepository")
 */
class Team
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
     * Name of the team.
     *
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * Description of the team.
     *
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * Path to the team image uploaded.
     *
     * @var string
     * @ORM\Column(name="image_url", type="string")
     */
    private $image_url;

    /**
     * Default location where de team plays and trains.
     *
     * @var Location
     * @ORM\ManyToOne(targetEntity="\TeamManager\EventBundle\Entity\Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    private $default_location;

    /**
     * Team manager.
     *
     * @var Player
     * @ORM\ManyToOne(targetEntity="\TeamManager\PlayerBundle\Entity\Player", inversedBy="managed_team")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id")
     */
    private $manager;

    /**
     * List of team players.
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="\TeamManager\TeamBundle\Entity\Team", cascade="persist", mappedBy="teams")
     */
    private $players;

    /**
     * Trainings of the team.
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\TeamManager\EventBundle\Entity\Training", mappedBy="team")
     */
    private $trainings;

    /**
     * Games of the team.
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\TeamManager\EventBundle\Entity\Game", mappedBy="team")
     */
    private $games;

    /**
     * Friendly games of the team.
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\TeamManager\EventBundle\Entity\GameFriendly", mappedBy="team")
     */
    private $games_friendly;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Goal", cascade="persist", mappedBy="team")
     */
    private $goals;

    /**
     *
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->trainings = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->games_friendly = new ArrayCollection();
        $this->$goals = new ArrayCollection();
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
     */
    public function setDescription($pDescription)
    {
        $this->description = $pDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @param string $pImageUrl
     */
    public function setImageUrl($pImageUrl)
    {
        $this->image_url = $pImageUrl;
        return $this;
    }

    /**
     * @return Location
     */
    public function getDefaultLocation()
    {
        return $this->default_location;
    }

    /**
     * @param Location $pDefaultLocation
     */
    public function setDefaultLocation($pDefaultLocation)
    {
        $this->default_location = $pDefaultLocation;
        return $this;
    }

    /**
     * @return Player
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param Player $pManager
     */
    public function setManager($pManager)
    {
        $this->manager = $pManager;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param Player $pPlayer
     * @return $this
     */
    public function addPlayer(Player $pPlayer)
    {
        $this->players[] = $pPlayer;
        return $this;
    }

    /**
     * @param Player $pPlayer
     * @return $this
     */
    public function removePlayer(Player $pPlayer)
    {
        $this->players->removeElement($pPlayer);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTrainings()
    {
        return $this->trainings;
    }

    /**
     * @param Training $pTraining
     * @return $this
     */
    public function addTraining(Training $pTraining)
    {
        $this->trainings[] = $pTraining;
        return $this;
    }

    /**
     * @param Training $pTraining
     * @return $this
     */
    public function removeTraining(Training $pTraining)
    {
        $this->trainings->removeElement($pTraining);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param Game $pGame
     * @return $this
     */
    public function addGame(Game $pGame)
    {
        $this->games[] = $pGame;
        return $this;
    }

    /**
     * @param Game $pGame
     * @return $this
     */
    public function removeGame(Game $pGame)
    {
        $this->games->removeElement($pGame);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGamesFriendly()
    {
        return $this->games_friendly;
    }

    /**
     * @param GameFriendly $pGame
     * @return $this
     */
    public function addGamesFriendly(GameFriendly $pGame)
    {
        $this->games_friendly[] = $pGame;
        return $this;
    }

    /**
     * @param GameFriendly $pGame
     * @return $this
     */
    public function removeGamesFriendly(GameFriendly $pGame)
    {
        $this->games_friendly->removeElement($pGame);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Add a goal.
     *
     * @param Goal $pGoal
     * @return Player
     */
    public function addGoal(Goal $pGoal)
    {
        $this->goals[] = $pGoal;
        return $this;
    }

    /**
     * Remove a goal.
     *
     * @param Goal $pGoal
     * @return Player
     */
    public function removeGoal(Goal $pGoal)
    {
        $this->goals->removeElement($pGoal);
        return $this;
    }

}