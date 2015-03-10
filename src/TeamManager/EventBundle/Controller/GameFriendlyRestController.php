<?php

namespace TeamManager\EventBundle\Controller;

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
use TeamManager\EventBundle\Entity\Game;
use TeamManager\EventBundle\Exception\InvalidGameFriendlyFormException;
use TeamManager\EventBundle\Form\GameType;
use TeamManager\PlayerBundle\Entity\Player;
use FOS\RestBundle\Controller\Annotations\View;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\TeamBundle\Entity\Team;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GameFriendlyRestController extends FOSRestController
{

    /**
     * Returns all friendly games.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Friendly Game API",
     *  output={
     *      "class"="TeamManager\EventBundle\Entity\GameFriendly",
     *      "collection"=true,
     *      "groups"={"Default"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "game"
     *  }
     * )
     *
     * @View( serializerGroups={ "Default" } )
     *
     * @Get("/", name="get_all", options={ "method_prefix" = false })
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return $this->getService()->getAll();
    }

    /**
     * Returns a friendly game by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Friendly Game API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Game id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\GameFriendly",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"Default"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when friendly game exists",
     *     404 = "Returned when the friendly game is not found"
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
     * Adds a new friendly game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Friendly Game API",
     *  input="TeamManager\EventBundle\Form\GameFriendlyType",
     *  statusCodes = {
     *      200 = "Returned when the friendly game has been created",
     *      400 = "Returned when the friendly game form has errors"
     *  }
     * )
     *
     * @View(
     *      serializerGroups={"Default"},
     *      template="TeamManagerTeamBundle:GameFriendly:gameFriendlyForm.html.twig",
     *      statusCode= Codes::HTTP_BAD_REQUEST,
     *      templateVar = "form"
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
            $form = new GameType();
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_game_friendly_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidGameFriendlyFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new friendly game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Friendly Game API",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the related team doesn't exist."
     *  },
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The game to which the friendly game will be related."
     *      }
     *  }
     * )
     *
     * @View(
     *      template="TeamManagerEventBundle:GameFriendly:gameFriendlyForm.html.twig",
     *      templateVar = "form"
     * )
     *
     * @Get("/new/{teamID}", name="new", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function newAction(Request $request, $teamID)
    {
        $team = $this->get('team_bundle.team.service')->getOr404($teamID);
        $game = new Game();
        $game->setTeam($team);

        return $this->createForm(
            new GameType(),
            $game,
            array(
                "action" => $this->generateUrl('api_game_friendly_post', array("access_token" => $_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing friendly game from the submitted data or create a new friendly game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Friendly Game API",
     *  input="TeamManager\EventBundle\Form\GameFriendlyType",
     *  statusCodes = {
     *      201 = "Returned when a new friendly game is created",
     *      204 = "Returned when the friendly game has been updated successfully",
     *      400 = "Returned when the form has errors"
     *  },
     *  requirements= {
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
     *       }
     *   }
     * )
     *
     * @View(
     *      serializerGroups={"Default"},
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
            $form = new GameType();
            if (!($game = $service->get($id))) {
                $game = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $game->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_game_friendly_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $game = $service->put(
                    $game,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidGameFriendlyFormException$exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing friendly game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Friendly Game API",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the friendly game doesn't exist."
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team id"
     *      }
     *  }
     * )
     *
     * @View(
     *      template="TeamManagerEventBundle:GameFriendly:gameFriendlyEditForm.html.twig",
     *      templateVar = "form"
     * )
     *
     * @Get("/edit/{id}", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $game = $this->getService()->getOr404($id);
        return $this->createForm(new GameType(), $game, array(
            "action" => $this->generateUrl(
                'api_game_put',
                [
                    'id' => $id,
                    'access_token' => $_GET['access_token']
                ]
            ),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a friendly game depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Friendly Game API",
     *  statusCodes = {
     *      200 = "Returned when friendly game has been successfully deleted.",
     *      404 = "Returned when friendly game doesn't exist."
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Player id"
     *      }
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
        $team = $service->getOr404($id);
        if (isset($team)) {
            return $service->delete($team);
        }
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return EntityServiceInterface
     */
    protected function getService()
    {
        return $this->container->get('event_bundle.game_friendly.service');
    }

}