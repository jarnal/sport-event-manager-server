<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TeamManager\EventBundle\Repository\Location;
use TeamManager\ResultBundle\Entity\GameResult;

/**
 * Event
 *
 * @ORM\Table(name="lms_event")
 * @ORM\Entity(repositoryClass="TeamManager\EventBundle\Repository\EventRepository")
 */
class Event
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
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="event_type", type="string")
     */
    private $event_type;

    /**
     * @var string
     * @ORM\Column(name="subscription_type", type="string")
     */
    private $subscription_type;

    /**
     * @var integer
     * @ORM\Column(name="limit", type="integer")
     */
    private $limit;

    /**
     * List of teams composing an event
     * For a game there will be two teams when for a training the will be only one team.
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="TeamManager\TeamBundle\Entity\Team", cascade="persist", inversedBy="games")
     * @ORM\JoinTable(name="tm_game_rel_team",
     *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id")}
     *      )
     */
    private $teams;

    /**
     * @var Location
     */
    private $location;

    /**
     * @var ArrayCollection
     */
    private $expectedPlayers;

    /**
     * @var ArrayCollection
     */
    private $missingPlayers;

    /**
     * @var ArrayCollection
     */
    private $presentPlayers;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
