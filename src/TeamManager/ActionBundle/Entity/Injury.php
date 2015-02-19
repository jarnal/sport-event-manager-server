<?php

namespace TeamManager\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Injury
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TeamManager\ActionBundle\Repository\InjuryRepository")
 */
class Injury
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
