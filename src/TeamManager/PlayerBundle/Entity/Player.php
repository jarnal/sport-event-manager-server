<?php

namespace TeamManager\PlayerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use TeamManager\ActionBundle\Entity\Card;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\ActionBundle\Entity\Injury;
use TeamManager\ResultBundle\Entity\Comment;
use TeamManager\ResultBundle\Entity\PlayerResult;
use TeamManager\SecurityBundle\Entity\Role;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Player
 *
 * @ORM\Table(name="tm_player")
 * @ORM\Entity(repositoryClass="TeamManager\PlayerBundle\Repository\PlayerRepository")
 */
class Player implements UserInterface, \Serializable
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
     * First name of the player.
     *
     * @var string
     * @ORM\Column(name="firstname", type="string")
     */
    private $firstname;

    /**
     * Last name of the player.
     *
     * @var string
     * @ORM\Column(name="lastname", type="string")
     */
    private $lastname;

    /**
     * User name of the player. The one used to connect to the application..
     *
     * @var string
     * @ORM\Column(name="username", type="string")
     */
    private $username;

    /**
     * Password of the user to connect to the application.
     *
     * @var string
     * @ORM\Column(name="password", type="string")
     */
    private $password;

    /**
     * Email of the player.
     *
     * @var string
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * Path to the player image uploaded.
     *
     * @var string
     * @ORM\Column(name="image_url", type="string")
     */
    private $image_url;

    /**
     * Jersey number of the player.
     *
     * @var integer
     * @ORM\Column(name="jersey_number", type="integer")
     */
    private $jersey_number;

    /**
     * Level of the player.
     *
     * @var integer
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * Defines if the the player is registered or if registration is pending.
     * By default the player can be added by a team manager.
     * After that he must pickup an username and a password to connect to the application.
     *
     * @var boolean
     * @ORM\Column(name="type", type="boolean")
     */
    private $registered;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="TeamManager\SecurityBundle\Entity\Role", cascade="persist")
     * @ORM\JoinTable(name="tm_player_rel_role",
     *      joinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $roles;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="TeamManager\TeamBundle\Entity\Team", cascade="persist", inversedBy="players")
     * @ORM\JoinTable(name="tm_player_rel_team",
     *      joinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id")}
     *      )
     */
    private $teams;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\TeamBundle\Entity\Team", cascade="persist", mappedBy="player")
     */
    private $managed_teams;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Goal", cascade="persist", mappedBy="player")
     */
    private $goals;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Card", cascade="persist", mappedBy="player")
     */
    private $cards;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ResultBundle\Entity\Comment", cascade="persist", mappedBy="player_receiver")
     */
    private $comments_received;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ResultBundle\Entity\Comment", cascade="persist", mappedBy="player_sender")
     */
    private $comments_left;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ActionBundle\Entity\Injury", cascade="persist", mappedBy="player")
     */
    private $injuries;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamManager\ResultBundle\Entity\PlayerResult", cascade="persist", mappedBy="player")
     */
    private $results;

    /**
     *
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->managed_teams = new ArrayCollection();
        $this->goals = new ArrayCollection();
        $this->cards = new ArrayCollection();
        $this->comments_received = new ArrayCollection();
        $this->comments_left = new ArrayCollection();
        $this->injuries = new ArrayCollection();
        $this->results = new ArrayCollection();
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
     * Get player first name.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set player first name.
     *
     * @param string $pFirstname
     * @return Player
     */
    public function setFirstname($pFirstname)
    {
        $this->firstname = $pFirstname;
        return $this;
    }

    /**
     * Get player last name.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set player last name.
     *
     * @param string $pLastname
     * @return Player
     */
    public function setLastname($pLastname)
    {
        $this->lastname = $pLastname;
        return $this;
    }

    /**
     * Get player username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set player username.
     *
     * @param string $pUsername
     * @return Player
     */
    public function setUsername($pUsername)
    {
        $this->username = $pUsername;
        return $this;
    }

    /**
     * Get player password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set player password.
     *
     * @param string $pPassword
     * @return Player
     */
    public function setPassword($pPassword)
    {
        $this->password = $pPassword;
        return $this;
    }

    /**
     * Get player email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set player email.
     *
     * @param string $pEmail
     * @return Player
     */
    public function setEmail($pEmail)
    {
        $this->email = $pEmail;
        return $this;
    }

    /**
     * Get player image path.
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Set player image path.
     *
     * @param string $pImageUrl
     * @return Player
     */
    public function setImageUrl($pImageUrl)
    {
        $this->image_url = $pImageUrl;
        return $this;
    }

    /**
     * Get player jersey number.
     *
     * @return int
     */
    public function getJerseyNumber()
    {
        return $this->jersey_number;
    }

    /**
     * Set player jersey number.
     *
     * @param int $pJerseyNumber
     * @return Player
     */
    public function setJerseyNumber($pJerseyNumber)
    {
        $this->jersey_number = $pJerseyNumber;
        return $this;
    }

    /**
     * Get player level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set player level.
     *
     * @param int $pLevel
     * @return Player
     */
    public function setLevel($pLevel)
    {
        $this->level = $pLevel;
        return $this;
    }

    /**
     * Get player registration.
     *
     * @return boolean
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * Set player registration.
     *
     * @param boolean $pRegistered
     * @return Player
     */
    public function setRegistered($pRegistered)
    {
        $this->registered = $pRegistered;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Add a role.
     *
     * @param Role $pRole
     * @return Player
     */
    public function addRole(Role $pRole)
    {
        $this->roles[] = $pRole;
        return $this;
    }

    /**
     * Remove a role.
     *
     * @param Role $pRole
     * @return Player
     */
    public function removeRole(Role $pRole)
    {
        $this->roles->removeElement($pRole);
        return $this;
    }

    /**
     * Get player team list.
     *
     * @return ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Add a team.
     *
     * @param Team $pTeam
     * @return Player
     */
    public function addTeam( Team $pTeam )
    {
        $this->teams[] = $pTeam;
        return $this;
    }

    /**
     * Remove a team.
     *
     * @param Team $pTeam
     * @return Player
     */
    public function removeTeam( Team $pTeam )
    {
        $this->teams->removeElement($pTeam);
        return $this;
    }

    /**
     * Get player managed team list.
     *
     * @return ArrayCollection
     */
    public function getManagedTeams()
    {
        return $this->managed_teams;
    }

    /**
     * Add a managed team.
     *
     * @param Team $pTeam
     * @return Player
     */
    public function addManagedTeam( Team $pTeam )
    {
        $this->managed_teams[] = $pTeam;
        return $this;
    }

    /**
     * Remove a managed team.
     *
     * @param Team $pTeam
     * @return Player
     */
    public function removeManagedTeam( Team $pTeam )
    {
        $this->managed_teams->removeElement($pTeam);
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

    /**
     * @return ArrayCollection
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Add a card.
     *
     * @param Card $pCard
     * @return Player
     */
    public function addCard(Card $pCard)
    {
        $this->cards[] = $pCard;
        return $this;
    }

    /**
     * Remove a card.
     *
     * @param Card $pCard
     * @return Player
     */
    public function removeCard(Card $pCard)
    {
        $this->cards->removeElement($pCard);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommentsReceived()
    {
        return $this->comments_received;
    }

    /**
     * Add a comment.
     *
     * @param Comment $pComment
     * @return Player
     */
    public function addCommentReceived(Comment $pComment)
    {
        $this->comments_received[] = $pComment;
        return $this;
    }

    /**
     * Remove a comment.
     *
     * @param Comment $pComment
     * @return Player
     */
    public function removeCommentReceived(Comment $pComment)
    {
        $this->comments_received->removeElement($pComment);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommentsLeft()
    {
        return $this->comments_left;
    }

    /**
     * Add a comment.
     *
     * @param Comment $pComment
     * @return Player
     */
    public function addCommentLeft(Comment $pComment)
    {
        $this->comments_left[] = $pComment;
        return $this;
    }

    /**
     * Remove a comment.
     *
     * @param Comment $pComment
     * @return Player
     */
    public function removeCommentLeft(Comment $pComment)
    {
        $this->comments_left->removeElement($pComment);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getInjuries()
    {
        return $this->injuries;
    }

    /**
     * Add a comment.
     *
     * @param Injury $pInjury
     * @return Player
     */
    public function addInjuries(Injury $pInjury)
    {
        $this->injuries[] = $pInjury;
        return $this;
    }

    /**
     * Remove a comment.
     *
     * @param Injury $pInjury
     * @return Player
     */
    public function removeInjuries(Injury $pInjury)
    {
        $this->injuries->removeElement($pInjury);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Add a result.
     *
     * @param PlayerResult $pResult
     * @return Player
     */
    public function addResult(PlayerResult $pResult)
    {
        $this->results[] = $pResult;
        return $this;
    }

    /**
     * Remove a result.
     *
     * @param PlayerResult $pResult
     * @return Player
     */
    public function removeResult(PlayerResult $pResult)
    {
        $this->results->removeElement($pResult);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(UserInterface $pUser)
    {
        return $pUser->getUsername() == $this->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($pRole)
    {
        return in_array( $pRole, $this->getRoles() );
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return "Gtrk-Prod2015";
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        //?
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return \serialize(array(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            ) = \unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getUsername();
    }

}
