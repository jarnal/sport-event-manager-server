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
use TeamManager\EventBundle\Exception\InvalidGameFormException;
use TeamManager\EventBundle\Form\GameType;
use TeamManager\PlayerBundle\Entity\Player;
use FOS\RestBundle\Controller\Annotations\View;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\TeamBundle\Entity\Team;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GameRestController extends FOSRestController
{

    /**
     * Returns all games.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Game API",
     *  output={
     *      "class"="TeamManager\EventBundle\Entity\Game",
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
     * @Get("/all", name="get_all", options={ "method_prefix" = false })
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return $this->getService()->getAll();
    }

    /**
     * Returns a game by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Game API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Game id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Game",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"Default"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when game exists",
     *     404 = "Returned when the game is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"Default"} )
     *
     * @Get("/get/{id}", name="get", options={ "method_prefix" = false })
     *
     * @return Player
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Game API",
     *  input="TeamManager\EventBundle\Form\GameType",
     *  statusCodes = {
     *      200 = "Returned when the game has been created",
     *      400 = "Returned when the game form has errors"
     *  }
     * )
     *
     * @View(
     *      serializerGroups={"Default"},
     *      template="TeamManagerTeamBundle:Team:gameForm.html.twig",
     *      statusCode= Codes::HTTP_BAD_REQUEST,
     *      templateVar = "form"
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
            $form = new GameType();
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_game_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidGameFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Game API",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the related team doesn't exist."
     *  },
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The game to which the game will be related."
     *      }
     *  }
     * )
     *
     * @View(
     *      template="TeamManagerEventBundle:Game:gameForm.html.twig",
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
                "action" => $this->generateUrl('api_game_post', array("access_token"=>$_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing game from the submitted data or create a new game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Game API",
     *  input="TeamManager\EventBundle\Form\GameType",
     *  statusCodes = {
     *      201 = "Returned when a new game is created",
     *      204 = "Returned when game has been updated successfully",
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
     * @Put("/put/{id}", name="put", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface|View
     */
    public function putAction(Request $request, $id)
    {
        $service = $this->getService();
        try {
            $form = new GameType();
            if ( !($game = $service->get($id)) ) {
                $game = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $game->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_game_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $game = $service->put(
                    $game,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidGameFormException$exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Game API",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the game doesn't exist."
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
     *      template="TeamManagerEventBundle:Game:gameEditForm.html.twig",
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
                    'id'=>$id,
                    'access_token'=>$_GET['access_token']
                ]
            ),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a game depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Game API",
     *  statusCodes = {
     *      200 = "Returned when game has been successfully deleted.",
     *      404 = "Returned when game doesn't exist."
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
     * @Delete("/delete/{id}", name="delete", options={ "method_prefix" = false })
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
     * Returns the appropriate service to handle related entity.
     *
     * @return EntityServiceInterface
     */
    protected function getService()
    {
        return $this->container->get('event_bundle.game.service');
    }

}