<?php
namespace TeamManager\ActionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\ActionBundle\Entity\Card;
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
        /*$manager->clear();

        $player = $this->getReference('player-1');
        $game = $this->getReference('game-1');

        $card1 = $this->buildCard($player, $game, Card::YELLOW_CARD);
        $card2 = $this->buildCard($player, $game, Card::RED_CARD);

        $manager->persist($card1);
        $manager->persist($card2);

        $manager->flush();

        $this->addReference('card-1', $card1);
        $this->addReference('card-2', $card2);

        static::$goals = array($card1, $card2);*/
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8;
    }

    /**
     * @param $player
     * @param $game
     * @param $type
     */
    private function buildCard($player, $game, $type)
    {
        /*$card = new Card();
        $card->setPlayer($player)
            ->setGame($game)
            ->setType($type)
        ;
        return $card;*/
    }
}