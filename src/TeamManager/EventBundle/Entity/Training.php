<?php

namespace TeamManager\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use TeamManager\TeamBundle\Entity\Team;

/**
 * Training
 *
 * @ORM\Entity(repositoryClass="TeamManager\EventBundle\Repository\TrainingRepository")
 * @ORM\Table(name="tm_training")
 *
 * @ExclusionPolicy("all")
 */
class Training extends Event
{

}
