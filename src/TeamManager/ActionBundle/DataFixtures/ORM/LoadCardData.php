<?php
namespace TeamManager\ActionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\ActionBundle\Entity\Card;
use TeamManager\CommonBundle\Entity\Location;
use TeamManager\TeamBundle\Entity\Team;

class LoadCardData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $cards;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $team1 = $this->getReference('team-1');
        $team2 = $this->getReference('team-2');

        $game1 = $team1->getGames()[0];
        $game2 = $team2->getGames()[0];

        $player1 = $team1->getPlayers()[0];
        $player2 = $team2->getPlayers()[0];

        $card1 = $this->buildCard($player1, $game1, Card::YELLOW_CARD);
        $card2 = $this->buildCard($player2, $game2, Card::RED_CARD);

        $manager->persist($card1);
        $manager->persist($card2);

        $manager->flush();

        $this->addReference('card-1', $card1);
        $this->addReference('card-2', $card2);

        static::$cards = array($card1, $card2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 7;
    }

    /**
     * @param $player
     * @param $game
     * @param $type
     */
    private function buildCard($player, $game, $type)
    {
        $card = new Card();
        $card->setPlayer($player)
            ->setGame($game)
            ->setType($type)
        ;
        return $card;
    }
}