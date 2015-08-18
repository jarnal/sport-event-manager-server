<?php
namespace TeamManager\ResultBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\ResultBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $comments;

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

        $comment1 = $this->buildComment($player1, $player2, $game1, "Pas mal joué.");
        $comment2 = $this->buildComment($player2, $player1, $game2, "Manque d'envie dans ce match la vérité.");

        $manager->persist($comment1);
        $manager->persist($comment2);

        $manager->flush();

        $this->addReference('comment-1', $comment1);
        $this->addReference('comment-2', $comment2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 11;
    }

    /**
     * @param $player
     * @param $game
     * @param $type
     */
    private function buildComment($sender, $receiver, $event, $content)
    {
        $comment = new Comment();
        $comment->setContent($content)
            ->setPlayerReceiver($receiver)
            ->setPlayerSender($sender)
            ->setEvent($event)
        ;
        return $comment;
    }
}