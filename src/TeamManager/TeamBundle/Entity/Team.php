<?php

namespace TeamManager\TeamBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TeamManager\PlayerBundle\Entity\Player;

/**
 * Team
 *
 * @ORM\Table()
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
     * @var
     */
    private $defaultLocation;

    /**
     *
     *
     * @var Player
     */
    private $manager;

    /**
     *
     *
     * @var ArrayCollection
     */
    private $players;

    /**
     *
     *
     * @var ArrayCollection
     */
    private $trainings;

    /**
     *
     *
     * @var ArrayCollection
     */
    private $games;

    /**
     *
     *
     * @var ArrayCollection
     */
    private $events;

    /**
     *
     *
     * @var ArrayCollection
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
}