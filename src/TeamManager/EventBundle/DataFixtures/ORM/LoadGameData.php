<?php
namespace TeamManager\PlayerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\EventBundle\Entity\Game;

class LoadGameData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $games;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $team = $this->getReference("team-1");

        $game1 = new Game();
        $game1->setName("name");
        $game1->setDescription("test");
        $game1->setDate(new \DateTime());
        $game1->setPlayerLimit(10);
        $game1->setLocation($team->getDefaultLocation());
        $game1->setOpponent(10);
        $game1->setSubscriptionType("test");
        $game1->setTeam($team);

        $manager->persist($game1);

        $manager->flush();

        $this->addReference('game-1', $game1);

        static::$games = array($game1);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}