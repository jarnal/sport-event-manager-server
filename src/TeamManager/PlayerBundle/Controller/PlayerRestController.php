<?php

namespace TeamManager\PlayerBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Form\FormTypeInterface;
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
use TeamManager\PlayerBundle\Exception\InvalidUserFormException;
use TeamManager\PlayerBundle\Form\PlayerType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PlayerRestController extends FOSRestController
{

    /**
     * Returns all players.
     *
     * @ApiDoc(
     *  resource=true,
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
        return $this->get("team_bundle.player.service")->getAll();
    }

    /**
     * Returns a player by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a player for a given id.",
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
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     * @Get("/get/{playerID}", name="get", options={ "method_prefix" = false })
     * @View( serializerGroups={"Default"} )
     * @return Player
     */
    public function getAction($playerID)
    {
        return $this->get("team_bundle.player.service")->get($playerID);
    }

    /**
     * Adds a new player.
     *
     * @ApiDoc(
     *  resource = true,
     *  description="Creates a new player.",
     *  input="TeamManager\PlayerBundle\Form\PlayerType",
     *  statusCodes = {
     *      200 = "Returned when the player has been created",
     *      400 = "Returned when the player form has errors"
     *  }
     * )
     * @Post("/post" , name="post", options={ "method_prefix" = false })
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerPlayerBundle:Player:playerForm.html.twig",
     *  statusCode= Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     */
    public function postAction(Request $request)
    {
        try {
            $form = new PlayerType();
            $player = $this->container->get('team_bundle.player.service')->post(
                $request->request->get($form->getName())
            );

            $routeOptions = array(
                'playerID' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_player_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidUserFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new player.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * @View(
     *  template="TeamManagerPlayerBundle:Player:playerForm.html.twig",
     * )
     *
     * @return FormTypeInterface
     */
    public function newAction()
    {
        return $this->createForm(new PlayerType());
    }

}

//curl -v -H "Accept: application/json" -H "Content-type: application/json" -X POST -d '{"player":{"firstname":"firstname", "username":"foo", "email": "foo@example.org", "password":"hahaha"}}' http://www.teammanager.com/web/app_dev.php/api/player/post