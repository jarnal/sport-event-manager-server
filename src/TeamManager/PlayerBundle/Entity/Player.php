<?php

namespace TeamManager\PlayerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="tm_player")
 * @ORM\Entity(repositoryClass="TeamManager\PlayerBundle\Repository\PlayerRepository")
 */
class Player
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
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $lastname;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $firstname;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $password;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $image_url;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $nickname;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $number;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $email;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="integer")
     */
    private $level;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $subscribed;

    /**
     *
     *
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $registered;


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
