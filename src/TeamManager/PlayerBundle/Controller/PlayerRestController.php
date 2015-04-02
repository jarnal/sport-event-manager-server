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
     *      "collectionName" = "players",
     *      "groups"={"Default"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      }
     *  }
     * )
     *
     * @View( serializerGroups={ "PlayerGlobal" } )
     *
     * @Get("/", name="get_all", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function getAllAction()
    {
        if( $this->getUser() ){
            return array("players"=>$this->getService()->getAll());
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
     * @View( serializerGroups={"PlayerDetails"} )
     *
     * @Get("/{id}", name="get", options={"method_prefix" = false}, requirements={"id"="\d+"})
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
     * @Get("/{id}/edit", name="edit", options={"method_prefix" = false})
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $player = $this->getService()->getOr404($id);
        return $this->createForm(new PlayerType(), $player, array(
            "action" => $this->generateUrl('api_player_put', array('id'=>$id, "access_token"=>$_GET["access_token"])),
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
     *      "class"="\TeamManager\EventBundle\Entity\Event",
     *      "collection"=true,
     *      "collectionName" = "events",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"EventPlayer", "TeamGlobal", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventPlayer", "TeamGlobal", "LocationGlobal"} )
     *
     * @Get("/{id}/events", name="events", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return array
     */
    public function listEventsAction($id)
    {
        $this->getService()->getOr404($id, false);
        $events = $this->get('event_bundle.event.service')->getPlayerEvents($id);
        return array("events"=>$events);
    }

    /**
     * Returns all events for a given player and for a specific season.
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
     *          "name"="season",
     *          "dataType"="string",
     *          "description"="Season name (2014-2015 format)"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Event",
     *      "collection"=true,
     *      "collectionName" = "events",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"Default", "EventPlayer", "TeamGlobal", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"Default", "EventPlayer", "TeamGlobal", "LocationGlobal"} )
     *
     * @Get("/{playerID}/season/{season}/events", name="events_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listEventsBySeasonAction($playerID, $season)
    {
        $this->getService()->getOr404($playerID, false);
        $events = $this->get('event_bundle.event.service')->getPlayerEventsForSeason($playerID, $season);
        return array("events"=>$events);
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
     *      "collection"=true,
     *      "collectionName" = "games",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"EventPlayer", "TeamGlobal", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventPlayer", "TeamGlobal", "LocationGlobal"} )
     *
     * @Get("/{id}/games", name="games", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGamesAction($id)
    {
        $this->getService()->getOr404($id, false);
        $games = $this->get('event_bundle.game.service')->getPlayerGames($id);
        return array("games"=>$games);
    }

    /**
     * Returns all games for a given player and for a specific season.
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
     *          "name"="season",
     *          "dataType"="string",
     *          "description"="Season name (2014-2015 format)"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Game",
     *      "collection"=true,
     *      "collectionName" = "games",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"EventPlayer", "TeamGlobal", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventPlayer", "TeamGlobal", "LocationGlobal"} )
     *
     * @Get("/{playerID}/season/{season}/games", name="games_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGamesBySeasonAction($playerID, $season)
    {
        $this->getService()->getOr404($playerID, false);
        $games = $this->get('event_bundle.game.service')->getPlayerGamesForSeason($playerID, $season);
        return array("games"=>$games);
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
     *      "collection"=true,
     *      "collectionName" = "games",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"EventPlayer", "TeamGlobal", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventPlayer", "TeamGlobal", "LocationGlobal"} )
     *
     * @Get("/{id}/friendly_games", name="friendly_games", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listFriendlyGamesAction($id)
    {
        $this->getService()->getOr404($id, false);
        $games = $this->get('event_bundle.game.service')->getPlayerFriendlyGames($id);
        return array("friendly_games"=>$games);
    }

    /**
     * Returns all friendly games for a given player and for a specific season.
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
     *          "name"="season",
     *          "dataType"="string",
     *          "description"="Season name (2014-2015 format)"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Game",
     *      "collection"=true,
     *      "collectionName" = "games",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"EventPlayer", "TeamGlobal", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventPlayer", "TeamGlobal", "LocationGlobal"} )
     *
     * @Get("/{playerID}/season/{season}/friendly_games", name="friendly_games_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listFriendlyGamesBySeasonAction($playerID, $season)
    {
        $this->getService()->getOr404($playerID, false);
        $games = $this->get('event_bundle.game.service')->getPlayerFriendlyGamesForSeason($playerID, $season);
        return array("friendly_games"=>$games);
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
     *      "class"="\TeamManager\EventBundle\Entity\Training",
     *      "collection"=true,
     *      "collectionName" = "trainings",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"EventPlayer", "TeamGlobal", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventPlayer", "TeamGlobal", "LocationGlobal"} )
     *
     * @Get("/{id}/trainings", name="trainings", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listTrainingsAction($id)
    {
        $this->getService()->getOr404($id, false);
        $trainings = $this->get('event_bundle.training.service')->getPlayerTrainings($id);
        return array("trainings"=>$trainings);
    }

    /**
     * Returns all trainings for a given player and for a specific season.
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
     *          "name"="season",
     *          "dataType"="string",
     *          "description"="Season name (2014-2015 format)"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Training",
     *      "collection"=true,
     *      "collectionName" = "trainings",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"EventPlayer", "TeamGlobal", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventPlayer", "TeamGlobal", "LocationGlobal"} )
     *
     * @Get("/{playerID}/season/{season}/trainings", name="trainings_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listTrainingsBySeasonAction($playerID, $season)
    {
        $this->getService()->getOr404($playerID, false);
        $trainings = $this->get('event_bundle.training.service')->getPlayerTrainingsForSeason($playerID, $season);
        return array("trainings"=>$trainings);
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
     *      "groups"={"CardPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"CardPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{id}/cards", name="cards", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listCardsAction($id)
    {
        $this->getService()->getOr404($id, false);
        $cards = $this->get('action_bundle.card.service')->getPlayerCards($id);
        return array("cards"=>$cards);
    }

    /**
     * Returns all cards for a given player and for a specific season.
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
     *          "name"="season",
     *          "dataType"="string",
     *          "description"="Season name (2014-2015 format)"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Card",
     *      "collection"=true,
     *      "collectionName" = "cards",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"CardPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"CardPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{playerID}/season/{season}/cards", name="cards_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listCardsBySeasonAction($playerID, $season)
    {
        $this->getService()->getOr404($playerID, false);
        $cards = $this->get('action_bundle.card.service')->getPlayerCardsForSeason($playerID, $season);
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
     *      "groups"={"CardPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"CardPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{playerID}/game/{gameID}/cards", name="game_cards", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listCardsForGameAction($playerID, $gameID)
    {
        $this->getService()->getOr404($playerID, false);
        $this->get('event_bundle.game.service')->getOr404($gameID, false);

        $cards = $this->get('action_bundle.card.service')->getPlayerCardsForGame($playerID, $gameID);
        return array("cards"=>$cards);
    }

    /**
     * Returns all goals for a given player.
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
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName"="goals",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"GoalPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"GoalPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{id}/goals", name="goals", options={"method_prefix" = false})
     *
     * @return array
     */
    public function listGoalsAction($id)
    {
        $this->getService()->getOr404($id, false);
        $goals = $this->get('action_bundle.goal.service')->getPlayerGoals($id);
        return array("goals"=>$goals);
    }

    /**
     * Returns all goals for a given player and for a specific season.
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
     *          "name"="season",
     *          "dataType"="string",
     *          "description"="Season name (2014-2015 format)"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName" = "goals",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"GoalPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"GoalPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{playerID}/season/{season}/goals", name="goals_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGoalsBySeasonAction($playerID, $season)
    {
        $this->getService()->getOr404($playerID, false);
        $goals = $this->get('action_bundle.goal.service')->getPlayerGoalsForSeason($playerID, $season);
        return array("goals"=>$goals);
    }

    /**
     * Returns all goals for a given player in a specific game.
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
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName"="goals",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"GoalPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"GoalPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{playerID}/game/{gameID}/goals", name="game_goals", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGoalsForGameAction($playerID, $gameID)
    {
        $this->getService()->getOr404($playerID, false);
        $this->get('event_bundle.game.service')->getOr404($gameID, false);

        $goals = $this->get('action_bundle.goal.service')->getPlayerGoalsForGame($playerID, $gameID);
        return array("goals"=>$goals);
    }

    /**
     * Returns all injuries for a given player.
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
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName"="injuries",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"InjuryPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"InjuryPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{id}/injuries", name="injuries", options={"method_prefix" = false})
     *
     * @return array
     */
    public function listInjuriesAction($id)
    {
        $this->getService()->getOr404($id, false);
        $injuries = $this->get('action_bundle.injury.service')->getPlayerInjuries($id);
        return array("injuries"=>$injuries);
    }

    /**
     * Returns all injuries for a given player and for a specific season.
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
     *          "name"="season",
     *          "dataType"="string",
     *          "description"="Season name (2014-2015 format)"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName" = "injuries",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"InjuryPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"InjuryPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{playerID}/season/{season}/injuries", name="injuries_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listInjuriesBySeasonAction($playerID, $season)
    {
        $this->getService()->getOr404($playerID, false);
        $injuries = $this->get('action_bundle.injury.service')->getPlayerInjuriesForSeason($playerID, $season);
        return array("injuries"=>$injuries);
    }

    /**
     * Returns all injuries for a given player in a specific game.
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
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName"="injuries",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"InjuryPlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"InjuryPlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{playerID}/game/{gameID}/injuries", name="game_injuries", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listInjuriesForGameAction($playerID, $gameID)
    {
        $this->getService()->getOr404($playerID, false);
        $this->get('event_bundle.game.service')->getOr404($gameID);

        $injuries = $this->get('action_bundle.injury.service')->getPlayerInjuriesForGame($playerID, $gameID);
        return array("injuries"=>$injuries);
    }

    /**
     * Returns all playtimes for a given player.
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
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName"="play_times",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"PlayTimePlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"PlayTimePlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{id}/play_times", name="play_times", options={"method_prefix" = false})
     *
     * @return array
     */
    public function listPlayTimesAction($id)
    {
        $this->getService()->getOr404($id, false);
        $playtimes = $this->get('action_bundle.play_time.service')->getPlayerPlayTimes($id);
        return array("play_times"=>$playtimes);
    }

    /**
     * Returns all playtimes for a given player and for a specific season.
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
     *          "name"="season",
     *          "dataType"="string",
     *          "description"="Season name (2014-2015 format)"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName" = "play_times",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"PlayTimePlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when player exists",
     *     404 = "Returned when the player is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"PlayTimePlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{playerID}/season/{season}/play_times", name="play_times_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listPlayTimesBySeasonAction($playerID, $season)
    {
        $this->getService()->getOr404($playerID, false);
        $playtimes = $this->get('action_bundle.play_time.service')->getPlayerPlayTimesForSeason($playerID, $season);
        return array("play_times"=>$playtimes);
    }

    /**
     * Returns all playtimes for a given player in a specific game.
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
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "collectionName"="play_times",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "groups"={"PlayTimePlayer", "TeamGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"PlayTimePlayer", "TeamGlobal", "EventMinimal"} )
     *
     * @Get("/{playerID}/game/{gameID}/play_times", name="game_play_times", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listPlayTimesForGameAction($playerID, $gameID)
    {
        $this->getService()->getOr404($playerID, false);
        $this->get('event_bundle.game.service')->getOr404($gameID, false);

        $playtimes = $this->get('action_bundle.play_time.service')->getPlayerPlayTimesForGame($playerID, $gameID);
        return array("play_times"=>$playtimes);
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