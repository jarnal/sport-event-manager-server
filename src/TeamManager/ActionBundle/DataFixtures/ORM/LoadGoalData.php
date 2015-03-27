<?php
namespace TeamManager\ActionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\ActionBundle\Entity\Card;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\TeamBundle\Entity\Team;

class LoadGoalData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $goals;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $player = $this->getReference('player-1');
        $game = $this->getReference('game-1');

        $goal1 = $this->buildGoal($player, $game, Goal::NORMAL);
        $goal2 = $this->buildGoal($player, $game, Goal::SPECIAL);

        $manager->persist($goal1);
        $manager->persist($goal2);

        $manager->flush();

        $this->addReference('goal-1', $goal1);
        $this->addReference('goal-2', $goal2);

        static::$goals = array($goal1, $goal2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8;
    }

    /**
     * Builds a new goal.
     *
     * @param $player
     * @param $game
     * @param $type
     *
     * return Goal
     */
    private function buildGoal($player, $game, $type)
    {
        $goal = new Goal();
        $goal->setPlayer($player)
            ->setGame($game)
            ->setType($type)
        ;
        return $goal;
    }
}