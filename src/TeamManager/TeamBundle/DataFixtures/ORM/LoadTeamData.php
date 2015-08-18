<?php
namespace TeamManager\TeamBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\TeamBundle\Entity\Team;

class LoadTeamData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $teamManager = $this->getReference('player-1');

        $location = new Location();
        $location->setName("Salle Pouet");
        $location->setAddress("47 rue Colin 69100 Villeurbanne");
        $location->setLatitude(45.772502);
        $location->setLongitude(4.874019);

        $manager->persist($location);
        $manager->flush();

        $arrPlayers1 = array();
        for($i=1; $i<=15; $i++) {
            $arrPlayers1[] = $this->getReference('player-'.$i);
        }

        $arrPlayers2 = array();
        for($i=15; $i<=30; $i++) {
            $arrPlayers2[] = $this->getReference('player-'.$i);
        }

        for($i=1; $i<=2; $i++)
        {
            $arrPlayers = ($i%2>0)? $arrPlayers1 : $arrPlayers2;
            $team = $this->buildTeam($i, $location, $teamManager, $arrPlayers);
            $manager->persist($team);
            $manager->flush();

            $this->addReference('team-'.$i, $team);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * @param $id
     * @param $location
     * @param $manager
     * @param $arrPlayers
     * @return Team
     */
    private function buildTeam($id, $location, $manager, $arrPlayers)
    {
        $team = new Team();
        $team->setName("team".$id);
        $team->setDescription("Description of team".$id);
        $team->setDefaultLocation($location);
        $team->setManager($manager);
        foreach($arrPlayers as $player)
        {
            $team->addPlayer($player);
        }
        return $team;
    }
}