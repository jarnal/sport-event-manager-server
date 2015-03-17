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

        static::$players = array();
        for($i=1; $i<=30; $i++)
        {
            $player = $this->buildUser($i);
            $manager->persist($player);
            $manager->flush();

            $this->addReference('player-'.$i, $player);

            static::$players[] = $player;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * @param $id
     * @return Player
     */
    private function buildUser($id)
    {
        $player = new Player();
        $player->setFirstName("TheFirstName".$id);
        $player->setLastName("TheLastName".$id);
        $player->setUserName("TheUserName".$id);
        $player->setPassword("ThePassword".$id);
        $player->setEmail("email".$id."@email.fr");
        $player->setJerseyNumber($id);
        $player->setLevel($id);
        $player->setApiKey("theapikey".$id);
        return $player;
    }
}