<?php
namespace TeamManager\ActionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\ActionBundle\Entity\PlayTime;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\TeamBundle\Entity\Team;

class LoadPlayTimeData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $playTimes;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $team1 = $this->getReference('team-1');
        $team2 = $this->getReference('team-2');

        $game1 = $this->getReference('game-1');
        $game2 = $this->getReference('game-2');

        $player1 = $team1->getPlayers()[0];
        $player2 = $team2->getPlayers()[0];

        $playTime1 = $this->buildPlayTime($player1, $game1, 20);
        $playTime2 = $this->buildPlayTime($player2, $game2, 30);

        $manager->persist($playTime1);
        $manager->persist($playTime2);

        $manager->flush();

        $this->addReference('playTime-1', $playTime1);
        $this->addReference('playTime-2', $playTime2);

        static::$playTimes = array($playTime1, $playTime2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 10;
    }

    /**
     * @param $player
     * @param $game
     * @param $duration
     */
    private function buildPlayTime($player, $game, $duration)
    {
        $playTime = new PlayTime();
        $playTime->setPlayer($player)
            ->setGame($game)
            ->setDuration($duration)
        ;
        return $playTime;
    }
}