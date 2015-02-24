<?php

namespace TeamManager\PlayerBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\Security\Acl\Exception\Exception;
use TeamManager\PlayerBundle\Entity\Player;
use FOS\RestBundle\Controller\Annotations\View;

class PlayerRestController extends FOSRestController
{

    /**
     * @Get("/all", name="get_all", options={ "method_prefix" = false })
     * @return JsonResponse
     *
     * @View( serializerGroups={ "Default" } )
     */
    public function getAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $playerRepository = $em->getRepository("TeamManagerPlayerBundle:Player");

        $player = new Player();
        $player->setFirstname("Test name");
        $player->setEmail("jarnal@hinnoya.fr");
        $player->setRegistered(false);

        $em->persist($player);
        $em->flush();

        $players = $playerRepository->findAll();

        return new JsonResponse($players);
    }

}