<?php

namespace TeamManager\TeamBundle\Controller;

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
use TeamManager\CommonBundle\Service\EntityRestService;
use TeamManager\CommonBundle\Service\EntityServiceInterface;
use TeamManager\PlayerBundle\Entity\Player;
use FOS\RestBundle\Controller\Annotations\View;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\TeamBundle\Entity\Team;
use TeamManager\TeamBundle\Exception\InvalidTeamFormException;
use TeamManager\TeamBundle\Form\TeamType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TeamRestController extends FOSRestController
{

    /**
     * Returns all teams.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  output={
     *      "class"="TeamManager\TeamBundle\Entity\Team",
     *      "collection"=true,
     *      "collectionName"="teams",
     *      "groups"={"TeamGlobal"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      }
     *  }
     * )
     *
     * @View( serializerGroups={ "TeamGlobal" } )
     *
     * @Get("/", name="get_all", options={ "method_prefix" = false })
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return array("teams"=>$this->getService()->getAll());
    }

    /**
     * Returns a team by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\TeamBundle\Entity\Team",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"TeamSpecific", "EventTeam", "LocationGlobal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"TeamSpecific", "EventTeam", "LocationGlobal", "PlayerGlobal"} )
     *
     * @Get("/{id}", name="get", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return Team
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new team.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Team API",
     *  input="TeamManager\TeamBundle\Form\TeamType",
     *  statusCodes = {
     *      200 = "Returned when the team has been created",
     *      400 = "Returned when the team form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerTeamBundle:Team:gameForm.html.twig",
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
            $form = new TeamType($this->getUser());
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_team_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidTeamFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new team.
     *
     * @ApiDoc(
     *   resource = true,
     *   section="Team API",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @View(
     *  template="TeamManagerTeamBundle:Team:teamForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/new", name="new", options={"method_prefix" = false})
     *
     * @return FormTypeInterface
     */
    public function newAction(Request $request)
    {
        $team = new Team();
        $team->setManager($this->getUser());
        return $this->createForm(
            new TeamType(),
            $team,
            array(
                "action" => $this->generateUrl('api_team_post', array("access_token"=>$_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing team from the submitted data or create a new team with a specific id.
     *
     * @ApiDoc(
     *   resource = true,
     *   section="Team API",
     *   input="TeamManager\TeamBundle\Form\TeamType",
     *   statusCodes = {
     *     201 = "Returned when a new team is created",
     *     204 = "Returned when team has been updated successfully",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerTeamBundle:Team:gameEditForm.html.twig",
     * )
     *
     * @Put("/{id}", name="put", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return FormTypeInterface|View
     */
    public function putAction(Request $request, $id)
    {
        $service = $this->getService();
        try {
            $form = new TeamType();
            if ( !($team = $service->get($id)) ) {
                $team = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $team->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_team_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $team = $service->put(
                    $team,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidTeamFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing team.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Team API",
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Team id"
     *   }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerTeamBundle:Team:teamEditForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/{id}/edit", name="edit", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $team = $this->getService()->getOr404($id);
        return $this->createForm(new TeamType($this->getUser()), $team, array(
            "action" => $this->generateUrl('api_team_put', array('id'=>$id, "access_token"=>$_GET["access_token"])),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a team depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Team API",
     *  statusCodes = {
     *   200 = "Returned when team has been successfully deleted.",
     *   404 = "Returned when team doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Team id"
     *   }
     *  }
     * )
     *
     * @Delete("/{id}", name="delete", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @param $id
     */
    public function deleteAction($id)
    {
        $service = $this->getService();
        $team = $service->getOr404($id);
        if ( isset($team) ) {
            return $service->delete($team);
        }
    }

    /**
     * Returns all events for a given team.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"Default"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{id}/events", name="events", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return array
     */
    public function listEventsAction($id)
    {
        $this->getService()->getOr404($id, false);
        $events = $this->get('event_bundle.event.service')->getTeamEvents($id);
        return array("events"=>$events);
    }

    /**
     * Returns all events for a given team and for a specific season.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"EventTeam", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{teamID}/season/{season}/events", name="events_season", options={"method_prefix" = false})
     *
     * @return array
     */
    public function listEventsBySeasonAction($teamID, $season)
    {
        $this->getService()->getOr404($teamID, false);
        $events = $this->get('event_bundle.event.service')->getTeamEventsForSeason($teamID, $season);
        return array("events"=>$events);
    }

    /**
     * Returns all games for a given team.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"EventTeam", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{id}/games", name="games", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGamesAction($id)
    {
        $this->getService()->getOr404($id, false);
        $games = $this->get('event_bundle.game.service')->getTeamGames($id);
        return array("games"=>$games);
    }

    /**
     * Returns all games for a given team and for a specific season.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"EventTeam", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{teamID}/season/{season}/games", name="games_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGamesBySeasonAction($teamID, $season)
    {
        $this->getService()->getOr404($teamID, false);
        $games = $this->get('event_bundle.game.service')->getTeamGamesForSeason($teamID, $season);
        return array("games"=>$games);
    }

    /**
     * Returns all friendly games for a given team.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"EventTeam", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{id}/friendly_games", name="friendly_games", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listFriendlyGamesAction($id)
    {
        $this->getService()->getOr404($id, false);
        $games = $this->get('event_bundle.game.service')->getTeamFriendlyGames($id);
        return array("friendly_games"=>$games);
    }

    /**
     * Returns all friendly games for a given team and for a specific season.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"EventTeam", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{teamID}/season/{season}/friendly_games", name="friendly_games_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listFriendlyGamesBySeasonAction($teamID, $season)
    {
        $this->getService()->getOr404($teamID, false);
        $games = $this->get('event_bundle.game.service')->getTeamFriendlyGamesForSeason($teamID, $season);
        return array("friendly_games"=>$games);
    }

    /**
     * Returns all trainings for a given team.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"EventTeam", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{id}/trainings", name="trainings", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listTrainingsAction($id)
    {
        $this->getService()->getOr404($id, false);
        $trainings = $this->get('event_bundle.training.service')->getTeamTrainings($id);
        return array("trainings"=>$trainings);
    }

    /**
     * Returns all trainings for a given team and for a specific season.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"EventTeam", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{teamID}/season/{season}/trainings", name="trainings_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listTrainingsBySeasonAction($teamID, $season)
    {
        $this->getService()->getOr404($teamID, false);
        $trainings = $this->get('event_bundle.training.service')->getTeamTrainingsForSeason($teamID, $season);
        return array("trainings"=>$trainings);
    }

    /**
     * Returns all cards for a given team.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"CardTeam", "PlayerGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"CardTeam", "PlayerGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{id}/cards", name="cards", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listCardsAction($id)
    {
        $this->getService()->getOr404($id, false);
        $cards = $this->get('action_bundle.card.service')->getTeamCards($id);
        return array("cards"=>$cards);
    }

    /**
     * Returns all cards for a given team and for a specific season.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"CardTeam", "PlayerGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"CardTeam", "PlayerGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{teamID}/season/{season}/cards", name="cards_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listCardsBySeasonAction($teamID, $season)
    {
        $this->getService()->getOr404($teamID, false);
        $cards = $this->get('action_bundle.card.service')->getTeamCardsForSeason($teamID, $season);
        return array("cards"=>$cards);
    }

    /**
     * Returns all cards for a given team in a specific game.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"CardTeam", "PlayerGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"CardTeam", "PlayerGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{teamID}/game/{gameID}/cards", name="game_cards", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listCardsForGameAction($teamID, $gameID)
    {
        $this->getService()->getOr404($teamID, false);
        $this->get('event_bundle.game.service')->getOr404($gameID);

        $cards = $this->get('action_bundle.card.service')->getTeamCardsForGame($teamID, $gameID);
        return array("cards"=>$cards);
    }

    /**
     * Returns all goals for a given team.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"GoalTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"GoalTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{id}/goals", name="goals", options={"method_prefix" = false})
     *
     * @return array
     */
    public function listGoalsAction($id)
    {
        $this->getService()->getOr404($id, false);
        $goals = $this->get('action_bundle.goal.service')->getTeamGoals($id);
        return array("goals"=>$goals);
    }

    /**
     * Returns all goals for a given team and for a specific season.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"GoalTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"GoalTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{teamID}/season/{season}/goals", name="goals_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGoalsBySeasonAction($teamID, $season)
    {
        $this->getService()->getOr404($teamID, false);
        $goals = $this->get('action_bundle.goal.service')->getTeamGoalsForSeason($teamID, $season);
        return array("goals"=>$goals);
    }

    /**
     * Returns all goals for a given team in a specific game.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"GoalTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"GoalTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{teamID}/game/{gameID}/goals", name="game_goals", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listGoalsForGameAction($teamID, $gameID)
    {
        $this->getService()->getOr404($teamID, false);
        $this->get('event_bundle.game.service')->getOr404($gameID);

        $goals = $this->get('action_bundle.goal.service')->getTeamGoalsForGame($teamID, $gameID);
        return array("goals"=>$goals);
    }

    /**
     * Returns all injuries for a given team.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"InjuryTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"InjuryTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{id}/injuries", name="injuries", options={"method_prefix" = false})
     *
     * @return array
     */
    public function listInjuriesAction($id)
    {
        $this->getService()->getOr404($id, false);
        $injuries = $this->get('action_bundle.injury.service')->getTeamInjuries($id);
        return array("injuries"=>$injuries);
    }

    /**
     * Returns all injuries for a given team and for a specific season.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"InjuryTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"InjuryTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{teamID}/season/{season}/injuries", name="injuries_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listInjuriesBySeasonAction($teamID, $season)
    {
        $this->getService()->getOr404($teamID, false);
        $injuries = $this->get('action_bundle.injury.service')->getTeamInjuriesForSeason($teamID, $season);
        return array("injuries"=>$injuries);
    }

    /**
     * Returns all injuries for a given team in a specific game.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"InjuryTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"InjuryTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{teamID}/game/{gameID}/injuries", name="game_injuries", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listInjuriesForGameAction($teamID, $gameID)
    {
        $this->getService()->getOr404($teamID, false);
        $this->get('event_bundle.game.service')->getOr404($gameID);

        $injuries = $this->get('action_bundle.injury.service')->getTeamInjuriesForGame($teamID, $gameID);
        return array("injuries"=>$injuries);
    }

    /**
     * Returns all playtimes for a given team.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"PlayTimeTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"PlayTimeTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{id}/play_times", name="play_times", options={"method_prefix" = false})
     *
     * @return array
     */
    public function listPlayTimesAction($id)
    {
        $this->getService()->getOr404($id, false);
        $playtimes = $this->get('action_bundle.play_time.service')->getTeamPlayTimes($id);
        return array("play_times"=>$playtimes);
    }

    /**
     * Returns all playtimes for a given team and for a specific season.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"PlayTimeTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"PlayTimeTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{teamID}/season/{season}/play_times", name="play_times_season", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listPlayTimesBySeasonAction($teamID, $season)
    {
        $this->getService()->getOr404($teamID, false);
        $playtimes = $this->get('action_bundle.play_time.service')->getTeamPlayTimesForSeason($teamID, $season);
        return array("play_times"=>$playtimes);
    }

    /**
     * Returns all playtimes for a given team in a specific game.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Team API",
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
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
     *      "groups"={"PlayTimeTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when all related entities exists",
     *     404 = "Returned when at least one of the related entities are not found"
     *   }
     * )
     *
     * @View( serializerGroups={"PlayTimeTeam", "TeamGlobal", "EventMinimal", "PlayerGlobal"} )
     *
     * @Get("/{teamID}/game/{gameID}/play_times", name="game_play_times", options={ "method_prefix" = false })
     *
     * @return array
     */
    public function listPlayTimesForGameAction($teamID, $gameID)
    {
        $this->getService()->getOr404($teamID, false);
        $this->get('event_bundle.game.service')->getOr404($gameID);

        $playtimes = $this->get('action_bundle.play_time.service')->getTeamPlayTimesForGame($teamID, $gameID);
        return array("play_times"=>$playtimes);
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return EntityServiceInterface
     */
    protected function getService()
    {
        return $this->container->get('team_bundle.team.service');
    }

}