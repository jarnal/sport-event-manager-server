<?php

namespace TeamManager\ActionBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

class CardRestController extends FOSRestController
{
    /**
     * @Get("/all", name="get_all", options={ "method_prefix" = false })
     * @return JsonResponse
     */
    public function getAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cardRepository = $em->getRepository("TeamManagerActionBundle:Card");

        $cardRepository->findAll();

        return new JsonResponse( $cardRepository );
    }

}