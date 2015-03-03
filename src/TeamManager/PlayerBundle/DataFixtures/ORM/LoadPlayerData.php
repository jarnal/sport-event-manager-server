<?php
namespace TeamManager\PlayerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\PlayerBundle\Entity\Player;

class LoadPlayerData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $players;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $player1 = new Player();
        $player1->setFirstName("TheFirstName1");
        $player1->setLastName("TheLastName1");
        $player1->setUserName("TheUserName1");
        $player1->setPassword("ThePassword1");
        $player1->setEmail("email1@email.fr");
        $player1->setJerseyNumber(1);
        $player1->setLevel(1);
        $player1->setApiKey("theapikeyoftheplayer1");

        $player2 = new Player();
        $player2->setFirstName("TheFirstName2");
        $player2->setLastName("TheLastName2");
        $player2->setUserName("TheUserName2");
        $player2->setPassword("ThePassword2");
        $player2->setEmail("email2@email.fr");
        $player2->setJerseyNumber(2);
        $player2->setLevel(2);
        $player2->setApiKey("theapikeyoftheplayer2");

        $manager->persist($player1);
        $manager->persist($player2);

        $manager->flush();

        $this->addReference('player-1', $player1);
        $this->addReference('player-2', $player2);

        static::$players = array($player1, $player2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}