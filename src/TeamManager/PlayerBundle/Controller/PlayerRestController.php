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
use TeamManager\PlayerBundle\Form\PlayerType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PlayerRestController extends FOSRestController
{

    /**
     * Returns all players.
     *
     * @ApiDoc(
     *  description="Returns all players.",
     *  output={
     *      "class"="TeamManager\PlayerBundle\Entity\Player",
     *      "collection"=true,
     *      "groups"={"Default"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "players"
     *  }
     * )
     * @Get("/all", name="get_all", options={ "method_prefix" = false })
     * @View( serializerGroups={ "Default" } )
     * @return JsonResponse
     */
    public function getAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $playerRepository = $em->getRepository("TeamManagerPlayerBundle:Player");

        $players = $playerRepository->findAll();

        return $players;
    }

    /**
     * Returns a player by id.
     *
     * @ApiDoc(
     *  description="Returns a player by id.",
     *  requirements={
     *      {
     *          "name"="playerID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\PlayerBundle\Entity\Player",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"Default"}
     *  }
     * )
     * @Get("/get/{playerID}", name="get", options={ "method_prefix" = false })
     * @View( serializerGroups={ "Default" } )
     * @return Player
     */
    public function getAction($playerID)
    {
        $em = $this->getDoctrine()->getManager();
        $playerRepository = $em->getRepository("TeamManagerPlayerBundle:Player");
        $player = $playerRepository->findOneById( $playerID );

        return $player;
    }

    /**
     * Adds a new player.
     *
     * @ApiDoc(
     *  description="Adds a new player.",
     *  requirements={
     *      {
     *          "name"="playerID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\PlayerBundle\Entity\Player",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"Default"}
     *  }
     * )
     * @Post("/new" , name="new", options={ "method_prefix" = false })
     */
    public function newAction()
    {
        return $this->processForm($pRequest, new Player());
    }

    /**
     * @param Player $pPlayer
     * @return Response
     */
    private function processForm(Request $pRequest, Player $pPlayer)
    {
        $statusCode = is_null($pPlayer->getId()) ? 201 : 204;

        $form = $this->createForm(new PlayerType(), $pPlayer);
        $form->submit($pRequest);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($pPlayer);
            $em->flush($pPlayer);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_player_get', array('playerID' => $pPlayer->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        $view = $this->view($form, 400);
        return $this->handleView($view);
    }

//curl -v -H "Accept: application/json" -H "Content-type: application/json" -X POST -d '{"player":{"username":"foo", "email": "foo@example.org", "password":"hahaha"}}' http://www.teammanager.com/web/app_dev.php/api/player/new

}