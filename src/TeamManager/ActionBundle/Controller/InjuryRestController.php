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
use TeamManager\ActionBundle\Entity\Injury;
use TeamManager\ActionBundle\Exception\InvalidInjuryFormException;
use TeamManager\ActionBundle\Form\InjuryType;
use TeamManager\ActionBundle\Service\InjuryService;
use TeamManager\CommonBundle\Service\EntityServiceInterface;

class InjuryRestController extends FOSRestController
{

    /**
     * Returns all injuries.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Injury API",
     *  output={
     *      "class"="TeamManager\ActionBundle\Entity\Injury",
     *      "collection"=true,
     *      "groups"={"InjurySpecific", "PlayerGlobal", "EventMinimal"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "injuries"
     *  }
     * )
     *
     * @View(serializerGroups={"InjurySpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/", name="get_all", options={"method_prefix" = false})
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return array("injuries"=>$this->getService()->getAll());
    }

    /**
     * Returns a injury by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Injury API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Injury id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Injury",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"InjurySpecific", "PlayerGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when injury exists",
     *     404 = "Returned when the injury is not found"
     *   }
     * )
     *
     * @View(serializerGroups={"InjurySpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/{id}", name="get", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return Injury
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new injury.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Injury API",
     *  input="TeamManager\ActionBundle\Form\InjuryType",
     *  statusCodes = {
     *      200 = "Returned when the injury has been created",
     *      400 = "Returned when the injury form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerActionBundle:Injury:injuryForm.html.twig",
     *  statusCode= Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @Post("/" , name="post", options={"method_prefix" = false})
     *
     * @return FormTypeInterface|View
     *
     */
    public function postAction(Request $request)
    {
        try {
            $form = new InjuryType($this->getUser());
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_team_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidInjuryFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new injury.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Injury API",
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  requirements={
     *      {
     *          "name"="playerID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Related player id"
     *      },
     *      {
     *          "name"="gameID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Related game id"
     *      }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerActionBundle:Injury:injuryForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/player/{playerID}/game/{gameID}/new", name="new", options={ "method_prefix" = false }, requirements={"playerID"="\d+", "gameID"="\d+"})
     *
     * @return FormTypeInterface
     */
    public function newAction(Request $request, $playerID, $gameID)
    {
        $player = $this-> get('player_bundle.player.service')->getOr404($playerID, false);
        $game = $this->get('event_bundle.game.service')->getOr404($gameID, false);

        $injury = new Injury();
        $injury->setPlayer($player);
        $injury->setGame($game);
        return $this->createForm(
            new InjuryType(),
            $injury,
            array(
                "action" => $this->generateUrl('api_injury_post', array("access_token"=>$_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing injury from the submitted data or create a new injury with a specific id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Injury API",
     *  input="TeamManager\ActionBundle\Form\InjuryType",
     *  statusCodes = {
     *      201 = "Returned when a new injury is created",
     *      204 = "Returned when injury has been updated successfully",
     *      400 = "Returned when the form has errors"
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Injury id"
     *      }
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerActionBundle:Injury:injuryEditForm.html.twig",
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
            $form = new InjuryType();
            if ( !($injury = $service->get($id)) ) {
                $injury = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $injury->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_injury_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $service->put(
                    $injury,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidInjuryFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing injury.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Injury API",
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Injury id"
     *   }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerActionBundle:Injury:injuryEditForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/{id}/edit", name="edit", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $injury = $this->getService()->getOr404($id);
        return $this->createForm(new InjuryType(), $injury, array(
            "action" => $this->generateUrl('api_injury_put', array('id'=>$id, "access_token"=>$_GET["access_token"])),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a injury depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Injury API",
     *  statusCodes = {
     *   200 = "Returned when injury has been successfully deleted.",
     *   404 = "Returned when injury doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Injury id"
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
        $injury = $service->getOr404($id);
        if ( isset($injury) ) {
            return $service->delete($injury);
        }
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return InjuryService
     */
    protected function getService()
    {
        return $this->container->get('action_bundle.injury.service');
    }

}