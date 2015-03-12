<?php
namespace TeamManager\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\EventBundle\Entity\GameFriendly;

class LoadGameFriendlyData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $games;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $team = $this->getReference("team-1");

        $date1 = new \DateTime();
        $date1->setTime(11, 00, 00);

        $date2 = new \DateTime();
        $date2->setTime(14, 00, 00);

        $game1 = new Game();
        $game1->setName("Friendly 1");
        $game1->setFriendly(true);
        $game1->setDescription("Friendly 1");
        $game1->setDate($date1);
        $game1->setPlayerLimit(10);
        $game1->setLocation($team->getDefaultLocation());
        $game1->setOpponent("Team Going To Die");
        $game1->setSubscriptionType("test");
        $game1->setTeam($team);

        $game2 = new Game();
        $game2->setName("Friendly 2");
        $game2->setFriendly(true);
        $game2->setDescription("Friendly 2");
        $game2->setDate($date2);
        $game2->setPlayerLimit(10);
        $game2->setLocation($team->getDefaultLocation());
        $game2->setOpponent("Team Going To Die");
        $game2->setSubscriptionType("test");
        $game2->setTeam($team);

        $manager->persist($game1);
        $manager->persist($game2);

        $manager->flush();

        $this->addReference('friendly-1', $game1);
        $this->addReference('friendly-2', $game2);

        static::$games = array($game1);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }
}