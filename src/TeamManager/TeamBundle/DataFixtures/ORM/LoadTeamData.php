<?php
namespace TeamManager\TeamBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\TeamBundle\Entity\Team;

class LoadTeamData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $teams;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $player = $this->getReference('player-1');

        $location = new Location();
        $location->setName("Salle Pouet");
        $location->setAddress("47 rue Colin 69100 Villeurbanne");
        $location->setLatitude(45.772502);
        $location->setLongitude(4.874019);

        $team1 = new Team();
        $team1->setName("team1");
        $team1->setDescription("Description of team1");
        $team1->setDefaultLocation($location);
        $team1->setManager($player);
        $team1->addPlayer($this->getReference('player-1'));
        $team1->addPlayer($this->getReference('player-2'));

        $team2 = new Team();
        $team2->setName("team1");
        $team2->setDescription("Description of team1");
        $team2->setDefaultLocation($location);
        $team2->setManager($player);
        $team2->addPlayer($this->getReference('player-1'));
        $team2->addPlayer($this->getReference('player-2'));

        $manager->persist($team1);
        $manager->persist($team2);

        $manager->flush();

        $this->addReference('team-1', $team1);
        $this->addReference('team-2', $team2);

        static::$teams = array($team1, $team2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}