<?php

namespace TeamManager\ResultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerResult
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TeamManager\ResultBundle\Repository\PlayerResultRepository")
 */
class PlayerResult
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
