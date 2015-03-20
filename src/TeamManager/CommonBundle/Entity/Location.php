<?php

namespace TeamManager\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Location
 *
 * @ORM\Table(name="tm_location")
 * @ORM\Entity(repositoryClass="TeamManager\CommonBundle\Repository\LocationRepository")
 *
 * @ExclusionPolicy("all")
 */
class Location
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     * @Groups({"LocationGlobal", "LocationSpecific"})
     */
    private $id;

    /**
     * Name of the location.
     *
     * @ORM\Column(name="name", type="string")
     * @var string
     *
     * @Expose
     * @Groups({"LocationGlobal", "LocationSpecific"})
     */
    private $name;

    /**
     * Complete address of the location.
     *
     * @ORM\Column(name="address", type="string")
     * @var string
     *
     * @Expose
     * @Groups({"LocationGlobal", "LocationSpecific"})
     */
    private $address;

    /**
     * Latitude of the location.
     *
     * @ORM\Column(name="latitude", type="integer")
     * @var integer
     *
     * @Expose
     * @Groups({"LocationSpecific"})
     */
    private $latitude;

    /**
     * Longitude of the location.
     *
     * @ORM\Column(name="longitude", type="integer")
     * @var integer
     *
     * @Expose
     * @Groups({"LocationSpecific"})
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

    /**
     * Get location name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set location name.
     *
     * @param string $pName
     * @return Location
     */
    public function setName($pName)
    {
        $this->name = $pName;
        return $this;
    }

    /**
     * Get location address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set location address.
     *
     * @param string $pAddress
     * @return Location
     */
    public function setAddress($pAddress)
    {
        $this->address = $pAddress;
        return $this;
    }

    /**
     * Get location latitude.
     *
     * @return int
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set location latitude.
     *
     * @param int $pLatitude
     * @return Location
     */
    public function setLatitude($pLatitude)
    {
        $this->latitude = $pLatitude;
        return $this;
    }

    /**
     * Get location longitude.
     *
     * @return int
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set location longitude.
     *
     * @param int $pLongitude
     * @return Location
     */
    public function setLongitude($pLongitude)
    {
        $this->longitude = $pLongitude;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
    }

}
