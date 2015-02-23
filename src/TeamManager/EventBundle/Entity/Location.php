<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Location
 *
 * @ORM\Table(name="tm_event_location")
 * @ORM\Entity(repositoryClass="TeamManager\EventBundle\Repository\LocationRepository")
 */
class Location
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
     * Location name.
     *
     * @ORM\Column(name="name", type="string")
     * @var string
     */
    private $name;

    /**
     * Latitude of the location.
     *
     * @ORM\Column(name="latitude", type="integer")
     * @var integer
     */
    private $latitude;

    /**
     * Longitude of the location.
     *
     * @ORM\Column(name="longitude", type="integer")
     * @var integer
     */
    private $longitude;


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
