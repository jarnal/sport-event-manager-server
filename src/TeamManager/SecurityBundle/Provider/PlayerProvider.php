<?php
// src/Acme/DemoBundle/Provider/UserProvider.php

namespace TeamManager\SecurityBundle\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NoResultException;

/**
 * Class UserProvider
 * @package TeamManager\SecurityBundle\Provider
 */
class PlayerProvider implements UserProviderInterface
{
    protected $playerRepository;

    /**
     * @param ObjectRepository $playerRepository
     */
    public function __construct(ObjectRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * @param string $playername
     * @return mixed
     */
    public function loadUserByUsername($playername)
    {
        $q = $this->playerRepository
            ->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $playername)
            ->setParameter('email', $playername)
            ->getQuery();

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin AcmeDemoBundle:User object identified by "%s".',
                $playername
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    /**
     * @param UserInterface $player
     * @return object
     */
    public function refreshUser(UserInterface $player)
    {
        $class = get_class($player);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->playerRepository->find($player->getId());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->playerRepository->getClassName() === $class
        || is_subclass_of($class, $this->playerRepository->getClassName());
    }
}