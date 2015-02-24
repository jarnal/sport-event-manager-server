<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Training
 *
 * @ORM\Entity
 * @ORM\Table(name="tm_training")
 */
class Training extends Event
{

    /**
     * Team participating to the game.
     *
     * @var Team
     * @ORM\ManyToOne(targetEntity="TeamManager\TeamBundle\Entity\Team", inversedBy="trainings")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * Get team participating to the training.
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set team participating to the training.
     *
     * @param Team $pTeam
     * @return Game
     */
    public function setTeam(Team $pTeam)
    {
        $this->team = $pTeam;
        return $this;
    }

}
