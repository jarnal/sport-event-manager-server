<?php
namespace TeamManager\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\EventBundle\Entity\Event;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\TeamBundle\Entity\Team;

class LoadGameData extends AbstractFixture implements OrderedFixtureInterface
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

        for($i=1; $i<=15; $i++)
        {
            $team = ($i%2>0||$i==0||$i==3)? $team1 : $team2;
            $season = ($i%2>0||$i==0||$i==3)? $season1 : $season2;
            $game = $this->buildGame($i, $team, $season);
            $manager->persist($game);

            $this->addReference('game-'.$i, $game);
        }

        $manager->persist($team1);
        $manager->persist($team2);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * @param $id
     * @param $team Team
     * @param $season
     * @return Game
     */
    private function buildGame($id, $team, $season)
    {
        $date = new \DateTime();
        $date->setTime($id, 00, 00);
        $game = new Game();

        $expectedPlayers = $team->getPlayers();
        $presentPlayers = $expectedPlayers->slice(0, 9);
        $missingPlayers = $expectedPlayers->slice(10, 14);
        $game->setExpectedPlayers($expectedPlayers);
        $game->setPresentPlayers($presentPlayers);
        $game->setMissingPlayers($missingPlayers);

        $game->setName("Game ".$id);
        $game->setDescription("Game ".$id);
        $game->setType(Event::GAME);
        $game->setDate($date);
        $game->setPlayerLimit(10);
        $game->setLocation($team->getDefaultLocation());
        $game->setOpponent("Team Going To Die ".$id);
        $game->setSubscriptionType("Game ".$id);
        $game->setTeam($team);
        $game->setSeason($season);
        $game->setTeamScore(2);
        $game->setOpponentScore(1);
        return $game;
    }
}