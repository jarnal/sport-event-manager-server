<?php

namespace TeamManager\PlayerBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class PlayerService extends EntityRestService
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
     * Returns all events of a given player.
     *
     * @param $id
     * @return ArrayCollection
     */
    public function listEvents($id)
    {
        $player = $this->getOr404($id);
        return $this->repository->getPlayerEvents($id);
    }

    /**
     * Returns all games of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listGames($id)
    {
        $player = $this->getOr404($id);
        return $this->repository->getPlayerGames($id);
    }

    /**
     * Returns all friendly games of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listFriendlyGames($id)
    {
        $player = $this->getOr404($id);
        return $this->repository->getPlayerFriendlyGames($id);
    }

    /**
     * Returns all trainings of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listTrainings($id)
    {
        $player = $this->getOr404($id);
        return $this->repository->getPlayerTrainings($id);
    }

    /**
     * Returns all teams of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listTeams($id)
    {
        $player = $this->getOr404($id);
        return $player->getTeams();
    }

    /**
     * Returns all teams of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listManagedTeams($id)
    {
        $player = $this->getOr404($id);
        return $player->getManagedTeams();
    }

    /**
     * Returns all goals of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listGoals($id)
    {
        $player = $this->getOr404($id);
        return $player->getGoals();
    }

    /**
     * Returns all cards of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listCards($id)
    {
        $player = $this->getOr404($id);
        return $player->getCards();
    }

    /**
     * Returns all received comments of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listCommentsReceived($id)
    {
        $player = $this->getOr404($id);
        return $player->getCommentsReceived();
    }

    /**
     * Returns all sent comments of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listCommentsSent($id)
    {
        $player = $this->getOr404($id);
        return $player->getCommentsSent();
    }

    /**
     * Returns all received notes of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listNotesReceived($id)
    {
        $player = $this->getOr404($id);
        return $player->getNotesReceived();
    }

    /**
     * Returns all sent notes of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listNotesSent($id)
    {
        $player = $this->getOr404($id);
        return $player->getNotesSent();
    }

    /**
     * Returns all injuries of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listInjuries($id)
    {
        $player = $this->getOr404($id);
        return $player->getInjuries();
    }

    /**
     * Returns all play times of a given player.
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function listPlayTimes($id)
    {
        $player = $this->getOr404($id);
        return $player->getPlayTimes();
    }

}