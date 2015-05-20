<?php

namespace TeamManager\CommonBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TeamManager\EventBundle\Exception\InvalidGameFormException;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\PlayerBundle\Exception\InvalidUserFormException;
use TeamManager\PlayerBundle\Form\PlayerType;
use TeamManager\PlayerBundle\Service\PlayerServiceInterface;

abstract class SecurityService implements EntityServiceInterface
{

    protected $em;
    protected $entityClass;
    protected $repository;

    /**
     *
     *
     * @param ObjectManager $pEntityManager
     * @param $pEntityClass
     */
    public function __construct(ObjectManager $pEntityManager, $pEntityClass)
    {
        $this->em = $pEntityManager;
        $this->entityClass = $pEntityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
    }

    /**
     *
     *
     * @param $id
     * @return mixed
     */
    /*public function getAll()
    {
        return $this->repository->findAll();
    }*/

    /**
     *
     *
     * @param $id
     */
    public function getByName($name)
    {
        return $this->repository->findOneByName($name);
    }

    /**
     * @param $id
     */
    public function getOr404($name)
    {
        if (!($entity = $this->getByName($name))) {
            throw new NotFoundHttpException(sprintf('The player \'%s\' was not found.',$name));
        }

        return $entity;
    }

}