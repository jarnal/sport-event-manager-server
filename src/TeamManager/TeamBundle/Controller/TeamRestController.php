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
use TeamManager\PlayerBundle\Exception\InvalidUserFormException;
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
     *  output={
     *      "class"="TeamManager\TeamBundle\Entity\Team",
     *      "collection"=true,
     *      "groups"={"Default"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "team"
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
     * Returns a team by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  requirements={
     *      {
     *          "name"="teamID",
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
     *      "groups"={"Default"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when team exists",
     *     404 = "Returned when the team is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"Default"} )
     *
     * @Get("/get/{teamID}", name="get", options={ "method_prefix" = false })
     *
     * @return Player
     */
    public function getAction($teamID)
    {
        return $this->getService()->getOr404($teamID);
    }

    /**
     * Adds a new team.
     *
     * @ApiDoc(
     *  resource = true,
     *  input="TeamManager\TeamBundle\Form\TeamType",
     *  statusCodes = {
     *      200 = "Returned when the team has been created",
     *      400 = "Returned when the team form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerTeamBundle:Team:teamForm.html.twig",
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
        /*try {
            $form = new TeamType();
            $player = $this->container->get('team_bundle.player.service')->post(
                $request->request->get($form->getName())
            );

            $routeOptions = array(
                'teamID' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_player_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidUserFormException $exception) {
            return $exception->getForm();
        }*/
    }

    /**
     * Builds the form to use to create a new team.
     *
     * @ApiDoc(
     *   resource = true,
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
     * @Get("/new", name="new", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function newAction()
    {
        /*return $this->createForm(new TeamType());*/
    }

    /**
     * Update existing team from the submitted data or create a new team with a specific id.
     *
     * @ApiDoc(
     *   resource = true,
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
     *  template="TeamManagerTeamBundle:Team:teamEditForm.html.twig",
     * )
     *
     * @Put("/put/{teamID}", name="put", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface|View
     */
    public function putAction(Request $request, $teamID)
    {
        /*$service = $this->container->get('team_bundle.player.service');
        try {
            $form = new TeamType();
            if ( !($player = $service->get($teamID)) ) {
                $player = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'teamID' => $player->getId(),
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
        }*/
    }

    /**
     * Builds the form to use to update an existing team.
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="teamID",
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
     * @Get("/edit/{teamID}", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($teamID)
    {
        /*$service = $this->container->get('team_bundle.player.service');
        $player = $service->get($teamID);
        return $this->createForm(new TeamType(), $player, array(
            "action" => $this->generateUrl( 'api_team_put' , ['teamID'=>$teamID] ),
            "method" => "PUT"
        ));*/
    }

    /**
     * Deletes a team depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *   200 = "Returned when team has been successfully deleted.",
     *   404 = "Returned when team doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="teamID",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Player id"
     *   }
     *  }
     * )
     *
     * @Delete("/delete/{teamID}", name="delete", options={ "method_prefix" = false })
     *
     * @param $teamID
     */
    public function deleteAction($teamID)
    {
        /*$player = $this->getOr404($teamID);
        if ( isset($player) ) {
            $service = $this->container->get('team_bundle.player.service');
            return $service->delete( $player );
        }*/
    }

    /**
     * Fetchs the Player or throw a 404 exception.
     *
     * @param int $teamID
     * @return PlayerInterface
     * @throws NotFoundHttpException
     */
    protected function getOr404($teamID)
    {
        if (!($player = $this->container->get('team_bundle.player.service')->get($teamID))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$teamID));
        }

        return $player;
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