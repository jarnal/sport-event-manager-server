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
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * Path to the team image uploaded.
     *
     * @var string
     * @ORM\Column(name="image_url", type="string", nullable=true)
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
     * @ORM\ManyToMany(targetEntity="\TeamManager\PlayerBundle\Entity\Player", cascade="persist", mappedBy="teams")
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
        return $this;
    }

    /**
     * Get team trainings list.
     *
     * @return ArrayCollection
     */
    public function getTrainings()
    {
        return $this->trainings;
    }

    /**
     * Add training in team trainings list.
     *
     * @param Training $pTraining
     * @return Team
     */
    public function addTraining(Training $pTraining)
    {
        $this->trainings[] = $pTraining;
        return $this;
    }

    /**
     * Remove training from team trainings list.
     *
     * @param Training $pTraining
     * @return Team
     */
    public function removeTraining(Training $pTraining)
    {
        $this->trainings->removeElement($pTraining);
        return $this;
    }

    /**
     * Get team games list.
     *
     * @return ArrayCollection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Add game in team games list.
     *
     * @param Game $pGame
     * @return Team
     */
    public function addGame(Game $pGame)
    {
        $this->games[] = $pGame;
        return $this;
    }

    /**
     * Remove game from team games list.
     *
     * @param Game $pGame
     * @return Team
     */
    public function removeGame(Game $pGame)
    {
        $this->games->removeElement($pGame);
        return $this;
    }

    /**
     * Get team friendly games list.
     *
     * @return ArrayCollection
     */
    public function getGamesFriendly()
    {
        return $this->games_friendly;
    }

    /**
     * Add friendly game in team friendly games list.
     *
     * @param GameFriendly $pGame
     * @return Team
     */
    public function addGamesFriendly(GameFriendly $pGame)
    {
        $this->games_friendly[] = $pGame;
        return $this;
    }

    /**
     * Remove friendly game from team friendly games list.
     *
     * @param GameFriendly $pGame
     * @return Team
     */
    public function removeGamesFriendly(GameFriendly $pGame)
    {
        $this->games_friendly->removeElement($pGame);
        return $this;
    }

    /**
     * Get team goals list.
     *
     * @return ArrayCollection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Add goal in goals list.
     *
     * @param Goal $pGoal
     * @return Team
     */
    public function addGoal(Goal $pGoal)
    {
        $this->goals[] = $pGoal;
        return $this;
    }

    /**
     * Remove goal from goals list.
     *
     * @param Goal $pGoal
     * @return Team
     */
    public function removeGoal(Goal $pGoal)
    {
        $this->goals->removeElement($pGoal);
        return $this;
    }

}