<?php

namespace TeamManager\CommonBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TeamManager\EventBundle\Exception\InvalidGameFormException;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\PlayerBundle\Exception\InvalidUserFormException;
use TeamManager\PlayerBundle\Form\PlayerType;
use TeamManager\PlayerBundle\Service\PlayerServiceInterface;

abstract class EntityRestService implements EntityServiceInterface
{

    protected $em;
    protected $formFactory;
    protected $entityClass;
    protected $repository;
    protected $formType;
    protected $formException;

    /**
     *
     *
     * @param ObjectManager $pEntityManager
     * @param $pEntityClass
     */
    public function __construct(ObjectManager $pEntityManager, FormFactoryInterface $pFormFactory, $pEntityClass, $pFormTypeClass, $pFormExceptionClass)
    {
        $this->em = $pEntityManager;
        $this->formFactory = $pFormFactory;
        $this->entityClass = $pEntityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
        $this->formType = $pFormTypeClass;
        $this->formException = $pFormExceptionClass;
    }

    /**
     *
     *
     * @param $id
     * @return mixed
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     *
     *
     * @param $id
     */
    public function get($id)
    {
        return $this->repository->findOneById($id, false);
    }

    /**
     * @param $id
     */
    public function getOr404($id)
    {
        if (!($entity = $this->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     *
     *
     * @param array $pParameters
     */
    public function post(array $pParameters)
    {
        return $this->processForm(new $this->entityClass(), $pParameters, 'POST');
    }

    /**
     *
     *
     * @param PlayerInterface $entity
     * @param array $parameters
     * @return mixed
     */
    public function put($entity, array $parameters)
    {
        return $this->processForm($entity, $parameters, 'PUT');
    }

    /**
     *
     *
     * @param PlayerInterface $entity
     * @param array $parameters
     * @return mixed
     */
    public function patch($entity, array $parameters)
    {
        return $this->processForm($entity, $parameters, 'PATCH');
    }

    /**
     *
     *
     * @param int $pUserID
     * @return mixed
     */
    public function delete($entity)
    {
        return $this->processDelete($entity);
    }

    /**
     * @param PlayerInterface $entity
     * @param array $pParameters
     * @param string $pMethod
     * @return mixed|PlayerInterface
     * @throws InvalidUserFormException
     */
    protected function processForm($entity, array $pParameters, $pMethod = "PUT")
    {
        $form = $this->formFactory->create(new $this->formType(), $entity, array('method' => $pMethod));
        $form->submit($pParameters, 'PUT'!=$pMethod);

        if ($this->isFormValid($form)) {

            $entity = $form->getData();
            $this->em->persist($entity);
            $this->em->flush($entity);

            return $entity;
        }

        throw new $this->formException('Invalid submitted data', $form);
    }

    /**
     * This method returns if the form is correct and the entity can be persisted.
     *
     * @param FormInterface $form
     * @return bool
     */
    protected function isFormValid(Form $form)
    {
        return $form->isValid();
    }

    /**
     *
     *
     * @param PlayerInterface $entity
     * @return PlayerInterface
     */
    protected function processDelete($entity)
    {
        $this->em->remove($entity);
        $this->em->flush($entity);

        return $entity;
    }

}