<?php

namespace TeamManager\EventBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class EventService extends EntityRestService
{

    protected $em;
    protected $formFactory;
    protected $entityClass;
    protected $repository;

    /**
     *
     *
     * @param ObjectManager $pEntityManager
     * @param $pEntityClass
     */
    public function __construct(ObjectManager $pEntityManager, FormFactoryInterface $pFormFactory, $pEntityClass)
    {
        $this->em = $pEntityManager;
        $this->formFactory = $pFormFactory;
        $this->entityClass = $pEntityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
    }

    /**
     * @param $playerID
     * @return array
     */
    public function getPlayerEvents($playerID)
    {
        return $this->repository->findEventsByPlayer($playerID);
    }

    /**
     *
     *
     * @param $playerID
     * @param $seasonID
     * @return array
     */
    public function getPlayerEventsForSeason($playerID, $seasonID)
    {
        //return $this->repository->findEventsByPlayerForSeason($playerID, $seasonID);
    }

    /**
     *
     *
     * @param $teamID
     * @return array
     */
    public function getTeamEvents($teamID)
    {
        return $this->repository->findEventsByTeam($teamID);
    }

    /**
     *
     *
     * @param $playerID
     * @param $seasonID
     * @return array
     */
    public function getTeamEventsForSeason($playerID, $seasonID)
    {
        //return $this->repository->findEventsByTeamForSeason($playerID, $seasonID);
    }

}