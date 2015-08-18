<?php
namespace TeamManager\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\EventBundle\Entity\Event;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\EventBundle\Entity\GameFriendly;
use TeamManager\EventBundle\Entity\Training;
use TeamManager\TeamBundle\Entity\Team;

class LoadTrainingData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $team1 = $this->getReference("team-1");
        $team2 = $this->getReference("team-2");

        $season1 = "2013-2014";
        $season2 = "2014-2015";

        for($i=1; $i<=10; $i++)
        {
            $team = ($i%2>0||$i==0||$i==3)? $team1 : $team2;
            $season = ($i%2>0||$i==0||$i==3)? $season1 : $season2;
            $training = $this->buildTraining($i, $team, $season);
            $manager->persist($training);
            $manager->flush();

            $this->addReference('training-'.$i, $training);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }

    /**
     * @param $id
     * @param $team Team
     * @param $season
     * @return Game
     */
    private function buildTraining($id, $team, $season)
    {
        $date = new \DateTime();
        $date->setTime($id, 00, 00);
        $training = new Training();

        $expectedPlayers = $team->getPlayers();
        $presentPlayers = $expectedPlayers->slice(0, 9);
        $missingPlayers = $expectedPlayers->slice(10, 14);
        $training->setExpectedPlayers($expectedPlayers);
        $training->setPresentPlayers($presentPlayers);
        $training->setMissingPlayers($missingPlayers);

        $training->setName("Training ".$id);
        $training->setDescription("Training ".$id);
        $training->setType(Event::TRAINING);
        $training->setDate($date);
        $training->setPlayerLimit(10);
        $training->setLocation($team->getDefaultLocation());
        $training->setSubscriptionType("Training ".$id);
        $training->setTeam($team);
        $training->setSeason($season);
        return $training;
    }
}