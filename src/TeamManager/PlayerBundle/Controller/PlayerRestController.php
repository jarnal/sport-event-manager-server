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
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Exception\Exception;
use TeamManager\PlayerBundle\Entity\Player;
use FOS\RestBundle\Controller\Annotations\View;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\PlayerBundle\Exception\InvalidUserFormException;
use TeamManager\PlayerBundle\Form\PlayerType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PlayerRestController extends FOSRestController
{

    /**
     * Returns all players.
     *
     * @ApiDoc(
     *  resource=true,
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
     *
     * @View( serializerGroups={ "Default" } )
     *
     * @Get("/all", name="get_all", options={ "method_prefix" = false })
     *
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
     *
     * @View( serializerGroups={"Default"} )
     *
     * @Get("/get/{playerID}", name="get", options={ "method_prefix" = false })
     *
     * @return Player
     */
    public function getAction($playerID)
    {
        return $this->getOr404($playerID);
    }

    /**
     * Adds a new player.
     *
     * @ApiDoc(
     *  resource = true,
     *  input="TeamManager\PlayerBundle\Form\PlayerType",
     *  statusCodes = {
     *      200 = "Returned when the player has been created",
     *      400 = "Returned when the player form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerPlayerBundle:Player:playerForm.html.twig",
     *  statusCode= Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @Post("/post" , name="post", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface|View
     *
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
     *
     * @View(
     *  template="TeamManagerPlayerBundle:Player:playerForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/new", name="new", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function newAction()
    {
        return $this->createForm(new PlayerType());
    }

    /**
     * Update existing player from the submitted data or create a new player with a specific id.
     *
     * @ApiDoc(
     *   resource = true,
     *   input="TeamManager\PlayerBundle\Form\PlayerType",
     *   statusCodes = {
     *     201 = "Returned when a new Player is created",
     *     204 = "Returned when Player has been updated successfully",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerPlayerBundle:Player:playerEditForm.html.twig",
     * )
     *
     * @Put("/put/{playerID}", name="put", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface|View
     */
    public function putAction(Request $request, $playerID)
    {
        $service = $this->container->get('team_bundle.player.service');
        try {
            $form = new PlayerType();
            if ( !($player = $service->get($playerID)) ) {
                $player = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'playerID' => $player->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_player_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $player = $service->put(
                    $player,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidUserFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing player.
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="playerID",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Player id"
     *   }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerPlayerBundle:Player:playerEditForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/edit/{playerID}", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($playerID)
    {
        $service = $this->container->get('team_bundle.player.service');
        $player = $service->get($playerID);
        return $this->createForm(new PlayerType(), $player, array(
            "action" => $this->generateUrl( 'api_player_put' , ['playerID'=>$playerID] ),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a player depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *   200 = "Returned when Player has been successfully deleted.",
     *   404 = "Returned when user doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="playerID",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Player id"
     *   }
     *  }
     * )
     *
     * @Delete("/delete/{playerID}", name="delete", options={ "method_prefix" = false })
     *
     * @param $playerID
     */
    public function deleteAction($playerID)
    {
        $player = $this->getOr404($playerID);
        if ( isset($player) ) {
            $service = $this->container->get('team_bundle.player.service');
            return $service->delete( $player );
        }
    }

    /**
     * Fetchs the Player or throw a 404 exception.
     *
     * @param int $playerID
     * @return PlayerInterface
     * @throws NotFoundHttpException
     */
    protected function getOr404($playerID)
    {
        if (!($player = $this->container->get('team_bundle.player.service')->get($playerID))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$playerID));
        }

        return $player;
    }

}

//curl -v -H "Accept: application/json" -H "Content-type: application/json" -X POST -d '{"player":{"firstname":"firstname", "username":"foo", "email": "foo@example.org", "password":"hahaha"}}' http://www.teammanager.com/web/app_dev.php/api/player/post
//location=`curl -X POST -d '{"player":{"firstname":"firstname", "username":"foo", "email": "foo@example.org", "password":"hahaha"}}' \ http://www.teammanager.com/web/app_dev.php/api/player/post \ --header "Content-Type:application/json" -v 2>&1 | grep Location | cut -d \  -f 3`;