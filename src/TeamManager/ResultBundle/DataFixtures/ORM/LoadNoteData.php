<?php
namespace TeamManager\ResultBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\ResultBundle\Entity\Note;

class LoadNoteData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $notes;

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

        $note1 = $this->buildNote($player1, $player2, $game1, 12);
        $note2 = $this->buildNote($player2, $player1, $game2, 16);

        $manager->persist($note1);
        $manager->persist($note2);

        $manager->flush();

        $this->addReference('note-1', $note1);
        $this->addReference('note-2', $note2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 12;
    }

    /**
     * @param $player
     * @param $game
     * @param $type
     */
    private function buildNote($sender, $receiver, $event, $content)
    {
        $comment = new Note();
        $comment->setContent($content)
            ->setPlayerReceiver($receiver)
            ->setPlayerSender($sender)
            ->setEvent($event)
        ;
        return $comment;
    }
}