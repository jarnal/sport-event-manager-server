<?php

namespace TeamManager\ActionBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\CommonBundle\Service\EntityRestService;

class GoalService extends EntityRestService
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
     */
    /*protected function processForm($entity, array $pParameters, $pMethod = "PUT")
    {
        $form = $this->formFactory->create(new $this->formType(), $entity, array('method' => $pMethod));
        $form->submit($pParameters);

        if ($form->isValid()) {


            $entity = $form->getData();

            $player = $entity->getPlayer();
            $game = $entity->getGame();
            if($entity->
            $this->em->persist($entity);
            $this->em->flush($entity);

            return $entity;
        }

        throw new $this->formException('Invalid submitted data', $form);
    }*/

}