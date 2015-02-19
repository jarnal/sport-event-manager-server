<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameFriendly
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TeamManager\EventBundle\Repository\GameFriendlyRepository")
 */
class GameFriendly
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
