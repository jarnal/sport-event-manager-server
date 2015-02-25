<?php

namespace TeamManager\PlayerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use TeamManager\ActionBundle\Entity\Card;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\ActionBundle\Entity\Injury;
use TeamManager\ActionBundle\Entity\PlayTime;
use TeamManager\ResultBundle\Entity\Comment;
use TeamManager\ResultBundle\Entity\Note;
use TeamManager\SecurityBundle\Entity\Role;
use TeamManager\TeamBundle\Entity\Team;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Player
 *
 * @ORM\Entity(repositoryClass="TeamManager\PlayerBundle\Repository\PartialPlayerRepository")
 * @ORM\Table(name="tm_partial_player")
 *
 * @ExclusionPolicy("all")
 */
class PartialPlayer
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
     * First name of the player.
     *
     * @var string
     * @ORM\Column(name="firstname", type="string")
     *
     * @Expose
     */
    protected $firstname;

    /**
     * Email of the player.
     *
     * @var string
     * @ORM\Column(name="email", type="string")
     *
     * @Expose
     */
    protected $email;

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

}
