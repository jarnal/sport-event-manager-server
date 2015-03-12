<?php
namespace TeamManager\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\EventBundle\Entity\GameFriendly;
use TeamManager\EventBundle\Entity\Training;

class LoadTrainingFriendlyData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $trainings;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $team = $this->getReference("team-1");

        $date1 = new \DateTime();
        $date1->setTime(18, 00, 00);

        $date2 = new \DateTime();
        $date2->setTime(22, 00, 00);

        $training1 = new Training();
        $training1->setName("Training 1");
        $training1->setDescription("Training 1");
        $training1->setDate($date1);
        $training1->setPlayerLimit(10);
        $training1->setLocation($team->getDefaultLocation());
        $training1->setTeam($team);

        $training2 = new Training();
        $training2->setName("Training 2");
        $training2->setDescription("Training 2");
        $training2->setDate($date2);
        $training2->setPlayerLimit(10);
        $training2->setLocation($team->getDefaultLocation());
        $training2->setTeam($team);

        $manager->persist($training1);
        $manager->persist($training2);

        $manager->flush();

        $this->addReference('training-1', $training1);
        $this->addReference('training-2', $training2);

        static::$trainings = array($training1, $training2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }
}