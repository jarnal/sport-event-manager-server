<?php

namespace TeamManager\ResultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamResult
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TeamManager\ResultBundle\Repository\TeamResultRepository")
 */
class TeamResult
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
