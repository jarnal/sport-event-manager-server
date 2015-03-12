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
use FOS\RestBundle\Controller\Annotations\Link;
use FOS\RestBundle\Controller\Annotations\Unlink;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Exception\Exception;
use TeamManager\CommonBundle\Service\EntityServiceInterface;
use TeamManager\EventBundle\Entity\Game;
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
     *  section="Player API",
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
     * @Get("/", name="get_all", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function getAllAction()
    {
        if( $this->getUser() ){
            return $this->get("team_bundle.player.service")->getAll();
        }
    }

    /**
     * Returns a player by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Player API",
     *  requirements={
     *      {
     *          "name"="id",
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
     * @Get("/{id}", name="get", options={ "method_prefix" = false })
     *
     * @return Player
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new player.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Player API",
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
     * @Post("/" , name="post", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface|View
     *
     */
    public function postAction(Request $request)
    {
        try {
            $form = new PlayerType();
            $player = $this->getService()->post(
                $request->request->get($form->getName())
            );

            $routeOptions = array(
                'id' => $player->getId(),
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
     *  resource = true,
     *  section="Player API",
     *  statusCodes = {
     *    200 = "Returned when successful"
     *  }
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
        return $this->createForm(new PlayerType(), null, array(
            "action" => $this->generateUrl('api_player_post'),
            "method" => "POST"
        ));
    }

    /**
     * Update existing player from the submitted data or create a new player with a specific id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Player API",
     *  input="TeamManager\PlayerBundle\Form\PlayerType",
     *  statusCodes = {
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
     * @Put("/{id}", name="put", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface|View
     */
    public function putAction(Request $request, $id)
    {
        $service = $this->getService();
        try {
            $form = new PlayerType();
            if ( !($player = $service->get($id)) ) {
                $player = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $player->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_player_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $player = $service->put(
                    $id,
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
     *  section="Player API",
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="id",
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
     * @Get("/edit/{id}", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $player = $this->getService()->getOr404($id);
        return $this->createForm(new PlayerType(), $player, array(
            "action" => $this->generateUrl( 'api_player_put' , ['id'=>$id] ),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a player depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Player API",
     *  statusCodes = {
     *   200 = "Returned when Player has been successfully deleted.",
     *   404 = "Returned when user doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Player id"
     *   }
     *  }
     * )
     *
     * @Delete("/{id}", name="delete", options={ "method_prefix" = false })
     *
     * @param $id
     */
    public function deleteAction($id)
    {
        $service = $this->getService();
        $player = $service->getOr404($id);
        if ( isset($player) ) {
            return $service->delete( $player );
        }
    }

    /**
     * Returns all events for a given player.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Player API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Game",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
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
     * @Get("/{id}/events", name="events", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listEventsAction($id)
    {
        return $this->getService()->listEvents($id);
    }

    /**
     * Returns all games for a given player.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Player API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Game",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
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
     * @Get("/{id}/games", name="games", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGamesAction($id)
    {
        return $this->getService()->listGames($id);
    }

    /**
     * Returns all friendly games for a given player.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Player API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Game",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
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
     * @Get("/{id}/friendly_games", name="friendly_games", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listFriendlyGamesAction($id)
    {
        return $this->getService()->listFriendlyGames($id);
    }

    /**
     * Returns all trainings for a given player.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Player API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Game",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
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
     * @Get("/{id}/trainings", name="trainings", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listTrainingsAction($id)
    {
        return $this->getService()->listTrainings($id);
    }

    /**
     * Returns all cards for a given player.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Player API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Card",
     *      "collection"=true,
     *      "collectionName"="cards",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
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
     * @Get("/{id}/cards", name="cards", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listCardsAction($id)
    {
        $this->getService()->getOr404($id);
        $cards = $this->get('action_bundle.card.service')->getPlayerCards($id);
        return array("cards"=>$cards);
    }

    /**
     * Returns all cards for a given player in a specific game.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Player API",
     *  requirements={
     *      {
     *          "name"="playerID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      },
     *      {
     *          "name"="gameID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Game id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Card",
     *      "collection"=true,
     *      "collectionName"="cards",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"Default"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"Default"} )
     *
     * @Get("/{playerID}/game/{gameID}/cards", name="game_cards", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listCardsForGameAction($playerID, $gameID)
    {
        $this->getService()->getOr404($playerID);
        $this->get('event_bundle.game.service')->getOr404($gameID);

        $cards = $this->get('action_bundle.card.service')->getCardsByPlayerForGame($playerID, $gameID);
        return array("cards"=>$cards);
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return EntityServiceInterface
     */
    protected function getService()
    {
        return $this->container->get('player_bundle.player.service');
    }

}