<?php

namespace TeamManager\ActionBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TeamManager\CommonBundle\Service\EntityServiceInterface;
use TeamManager\PlayerBundle\Entity\Player;

class CardRestController extends FOSRestController
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
     * @Get("/", name="get_all", options={ "method_prefix" = false })
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
     * @Get("/{id}", name="get", options={ "method_prefix" = false })
     *
     * @return Player
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
     * @Get("/new", name="new", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function newAction(Request $request)
    {
        $team = new Team();
        $team->setManager( $this->getUser() );
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
     * @Put("/{id}", name="put", options={ "method_prefix" = false })
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
     * @Get("/edit/{id}", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $team = $this->getService()->getOr404($id);
        return $this->createForm(new TeamType($this->getUser()), $team, array(
            "action" => $this->generateUrl( 'api_team_put' , ['id'=>$id] ),
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
        return $this->container->get('team_bundle.team.service');
    }

}