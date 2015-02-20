<?php

namespace TeamManager\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="TeamManager\SecurityBundle\Repository\PlayerRepository")
 * @ORM\Table(name="tm_role")
 */
class Role implements RoleInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="label", type="string", length=255)
     * @Assert\NotBlank()
     * @var string
     */
    private $label;


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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $pName
     * @return Role
     */
    public function setName($pName)
    {
        $this->name = $pName;
        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $pLabel
     * @return Role
     */
    public function setLabel($pLabel)
    {
        $this->label = $pLabel;
        return $this;
    }

    /**
     * Get Role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return \serialize(array(
            $this->id,
            $this->name
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name
            ) = \unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
    }
}
