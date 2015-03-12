<?php
namespace TeamManager\TeamBundle\DataFixtures\ORM;

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

        $player = $this->getReference('player-1');
        $game = $this->getReference('game-1');

        $card1 = new Card();
        $card1->setPlayer($player);
        $card1->setGame($game);
        $card1->setType(Card::YELLOW_CARD);

        $card2 = new Card();
        $card2->setPlayer($player);
        $card2->setGame($game);
        $card2->setType(Card::RED_CARD);

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
}