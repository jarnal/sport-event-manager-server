<?php

namespace TeamManager\PlayerBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use TeamManager\CommonBundle\Service\EntityRestService;

class PlayerService extends EntityRestService
{

    protected $encoderFactory;

    /**
     * @param ObjectManager $pEntityManager
     * @param FormFactoryInterface $pFormFactory
     * @param $pEntityClass
     * @param $pFormTypeClass
     * @param $pFormExceptionClass
     */
    public function __construct(ObjectManager $pEntityManager, FormFactoryInterface $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass, EncoderFactory $pEncoderFactory)
    {
        $this->encoderFactory = $pEncoderFactory;
        parent::__construct($pEntityManager, $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass);
    }

    /**
     * {@inheritdoc}
     */
    protected function processForm($entity, array $pParameters, $pMethod = "PUT")
    {
        $form = $this->formFactory->create(new $this->formType(), $entity, array('method' => $pMethod));
        $form->submit($pParameters, 'PUT'!=$pMethod);

        $factory = $this->encoderFactory;
        $encoder = $factory->getEncoder($entity);
        $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
        $entity->setPassword($password);

        if ($this->isFormValid($form)) {

            $entity = $form->getData();
            $this->em->persist($entity);
            $this->em->flush($entity);

            return $entity;
        }

        throw new $this->formException('Invalid submitted data', $form);
    }

    /**
     *
     *
     * @param $id
     */
    public function getByLoginPassword($login, $password)
    {
        return $this->repository->findOneBy(array("username"=>$login, "password"=>$password));
    }

    /**
     * @param $id
     */
    public function getByLoginPasswordOr404($login, $password)
    {
        if (!($entity = $this->getByLoginPassword($login, $password))) {
            throw new NotFoundHttpException(sprintf('The player \'%s\' was not found.',$login));
        }

        return $entity;
    }

}