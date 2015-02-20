<?php

namespace TeamManager\PlayerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
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
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="TeamManager\SecurityBundle\Entity\Role", cascade="persist")
     * @ORM\JoinTable(name="tm_player_rel_role",
     *      joinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $teams;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $goals;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $cards;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $comments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $results;

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
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    //------------------------------------------------------------------------------------------------------

    /**
     * Get player team list.
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResults()
    {
        return $this->results;
    }

    

    //------------------------------------------------------------------------------------------------------

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
