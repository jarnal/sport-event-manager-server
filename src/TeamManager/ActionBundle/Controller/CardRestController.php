<?php

namespace TeamManager\ActionBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CardRestController extends FOSRestController
{
    /**
     * @return JsonResponse
     */
    public function getAllCardsAction()
    {
        return new JsonResponse( "test" );
    }

    /**
     *
     *
     *
     * TODO : Add VirtualHost for project.
     *
     *
     *
     */

}