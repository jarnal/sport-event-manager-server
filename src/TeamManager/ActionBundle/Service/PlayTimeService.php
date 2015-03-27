<?php

namespace TeamManager\ActionBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class PlayTimeService extends EntityRestService
{

    /**
     * @param ObjectManager $pEntityManager
     * @param FormFactoryInterface $pFormFactory
     * @param $pEntityClass
     * @param $pFormTypeClass
     * @param $pFormExceptionClass
     */
    public function __construct(ObjectManager $pEntityManager, FormFactoryInterface $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass)
    {
        parent::__construct($pEntityManager, $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass);
    }

    /**
     * {@inheritdoc}
     * In the case of a card, the player has to be in the related game to allow card to be valid.
     * Impossible to add a card for a player that is not in the passed game.
     *
     * @param Form $form
     */
    protected function isFormValid(Form $form)
    {
        if(!$form->isValid()) return false;

        $entity = $form->getData();
        $game = $entity->getGame();
        $player = $entity->getPlayer();
        if($game->getExpectedPlayers()->contains($player)){
            return true;
        } else {
            $form->get('player')->addError(new FormError("injury.form.player.incorrect.game"));
        }
        return false;
    }

}