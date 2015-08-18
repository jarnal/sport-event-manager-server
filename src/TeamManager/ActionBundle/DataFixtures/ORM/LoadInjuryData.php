<?php
namespace TeamManager\ActionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\ActionBundle\Entity\Injury;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\TeamBundle\Entity\Team;

class LoadInjuryData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $injuries;

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

        $injury1 = $this->buildInjury($player1, $game1, Injury::NORMAL);
        $injury2 = $this->buildInjury($player2, $game2, Injury::SERIOUS);

        $manager->persist($injury1);
        $manager->persist($injury2);

        $manager->flush();

        $this->addReference('injury-1', $injury1);
        $this->addReference('injury-2', $injury2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 9;
    }

    /**
     * @param $player
     * @param $game
     * @param $type
     */
    private function buildInjury($player, $game, $type)
    {
        $injury = new Injury();
        $injury->setPlayer($player)
            ->setGame($game)
            ->setType($type)
        ;
        return $injury;
    }
}