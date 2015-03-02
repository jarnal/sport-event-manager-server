<?php

namespace TeamManager\PlayerBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\PlayerBundle\Exception\InvalidUserFormException;
use TeamManager\PlayerBundle\Form\PlayerType;
use TeamManager\PlayerBundle\Service\PlayerServiceInterface;

class PlayerService implements PlayerServiceInterface
{

    private $em;
    private $repository;
    private $formFactory;

    /**
     *
     *
     * @param ObjectManager $pEntityManager
     * @param $pEntityClass
     */
    public function __construct(ObjectManager $pEntityManager, $pEntityClass, FormFactoryInterface $pFormFactory)
    {
        $this->em = $pEntityManager;
        $this->repository = $this->em->getRepository($pEntityClass);
        $this->formFactory = $pFormFactory;
    }

    /**
     *
     *
     * @param $id
     * @return mixed
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     *
     *
     * @param $pUserID
     */
    public function get($pUserID)
    {
        return $this->repository->findOneById($pUserID);
    }

    /**
     *
     *
     * @param array $pParameters
     */
    public function post(array $pParameters)
    {
        return $this->processForm(new Player(), $pParameters, 'POST');
    }

    /**
     *
     *
     * @param PlayerInterface $player
     * @param array $parameters
     * @return mixed
     */
    public function put(PlayerInterface $player, array $parameters)
    {
        return $this->processForm($player, $parameters, 'PUT');
    }

    /**
     *
     *
     * @param PlayerInterface $player
     * @param array $parameters
     * @return mixed
     */
    public function patch(PlayerInterface $player, array $parameters)
    {
        return $this->processForm($player, $parameters, 'PATCH');
    }

    /**
     *
     *
     * @param int $pUserID
     * @return mixed
     */
    public function delete(PlayerInterface $player)
    {
        return $this->processDelete($player);
    }

    /**
     * @param PlayerInterface $pPlayer
     * @param array $pParameters
     * @param string $pMethod
     * @return mixed|PlayerInterface
     * @throws InvalidUserFormException
     */
    private function processForm(PlayerInterface $pPlayer, array $pParameters, $pMethod = "PUT")
    {
        $form = $this->formFactory->create(new PlayerType(), $pPlayer, array('method' => $pMethod));
        $form->submit($pParameters, 'PATCH' !== $pMethod);

        if ($form->isValid()) {

            $pPlayer = $form->getData();
            $this->em->persist($pPlayer);
            $this->em->flush($pPlayer);

            return $pPlayer;
        }

        throw new InvalidUserFormException( 'Invalid submitted data', $form );
    }

    /**
     *
     *
     * @param PlayerInterface $player
     * @return PlayerInterface
     */
    private function processDelete(PlayerInterface $player)
    {
        $this->em->remove($player);
        $this->em->flush($player);

        return $player;
    }

}