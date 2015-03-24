<?php

namespace TeamManager\EventBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class TrainingService extends EntityRestService
{

    /**
     * @param ObjectManager $pEntityManager
     * @param FormFactoryInterface $pFormFactory
     * @param $pEntityClass
     * @param $pFormTypeClass
     * @param $pFormExceptionClass
     */
    public function __construct(ObjectManager $pEntityManager, FormFactoryInterface $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass)
    {
        parent::__construct($pEntityManager, $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass);
    }

    /**
     * {@inheritdoc}
     */
    protected function processForm($entity, array $pParameters, $pMethod = "PUT")
    {
        $entity->setType("training");
        return parent::processForm($entity, $pParameters, $pMethod);
    }

    /**
     *
     *
     * @param $playerID
     * @return array
     */
    public function getPlayerTrainings($playerID)
    {
        return $this->repository->findTrainingsByPlayer($playerID);
    }

    /**
     *
     *
     * @param $playerID
     * @param $seasonID
     * @return array
     */
    public function getPlayerTrainingsForSeason($playerID, $season)
    {
        return $this->repository->findTrainingsForPlayerBySeason($playerID, $season);
    }

    /**
     *
     *
     * @param $teamID
     * @return array
     */
    public function getTeamTrainings($teamID)
    {
        return $this->repository->findTrainingsByTeam($teamID);
    }

    /**
     *
     *
     * @param $teamID
     * @param $seasonID
     * @return array
     */
    public function getTeamTrainingsForSeason($teamID, $season)
    {
        return $this->repository->findTrainingsForTeamBySeason($teamID, $season);
    }

}