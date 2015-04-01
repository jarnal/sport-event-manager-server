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
use TeamManager\ActionBundle\Entity\PlayTime;
use TeamManager\ActionBundle\Exception\InvalidPlayTimeFormException;
use TeamManager\ActionBundle\Form\PlayTimeType;
use TeamManager\ActionBundle\Service\PlayTimeService;
use TeamManager\CommonBundle\Service\EntityServiceInterface;

class PlayTimeRestController extends FOSRestController
{

    /**
     * Returns all play_times.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="PlayTime API",
     *  output={
     *      "class"="TeamManager\ActionBundle\Entity\PlayTime",
     *      "collection"=true,
     *      "groups"={"PlayTimeSpecific", "PlayerGlobal", "EventMinimal"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "play_times"
     *  }
     * )
     *
     * @View(serializerGroups={"PlayTimeSpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/", name="get_all", options={"method_prefix" = false})
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return array("play_times"=>$this->getService()->getAll());
    }

    /**
     * Returns a play_time by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="PlayTime API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="PlayTime id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\PlayTime",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"PlayTimeSpecific", "PlayerGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when play_time exists",
     *     404 = "Returned when the play_time is not found"
     *   }
     * )
     *
     * @View(serializerGroups={"PlayTimeSpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/{id}", name="get", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return PlayTime
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new play_time.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="PlayTime API",
     *  input="TeamManager\ActionBundle\Form\PlayTimeType",
     *  statusCodes = {
     *      200 = "Returned when the play_time has been created",
     *      400 = "Returned when the play_time form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerActionBundle:PlayTime:play_timeForm.html.twig",
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
            $form = new PlayTimeType($this->getUser());
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_team_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidPlayTimeFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new play_time.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="PlayTime API",
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
     *          "description"="Related play_time id"
     *      }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerActionBundle:PlayTime:playTimeForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/player/{playerID}/game/{gameID}/new", name="new", options={ "method_prefix" = false }, requirements={"playerID"="\d+", "gameID"="\d+"})
     *
     * @return FormTypeInterface
     */
    public function newAction(Request $request, $playerID, $gameID)
    {
        $player = $this-> get('player_bundle.player.service')->getOr404($playerID);
        $game = $this->get('event_bundle.game.service')->getOr404($gameID);

        $play_time = new PlayTime();
        $play_time->setPlayer($player);
        $play_time->setGame($game);
        return $this->createForm(
            new PlayTimeType(),
            $play_time,
            array(
                "action" => $this->generateUrl('api_playtime_post', array("access_token"=>$_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing play_time from the submitted data or create a new play_time with a specific id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="PlayTime API",
     *  input="TeamManager\ActionBundle\Form\PlayTimeType",
     *  statusCodes = {
     *      201 = "Returned when a new play_time is created",
     *      204 = "Returned when play_time has been updated successfully",
     *      400 = "Returned when the form has errors"
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="PlayTime id"
     *      }
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerActionBundle:PlayTime:playTimeEditForm.html.twig",
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
            $form = new PlayTimeType();
            if ( !($play_time = $service->get($id)) ) {
                $play_time = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $play_time->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_playtime_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $service->put(
                    $play_time,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidPlayTimeFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing play_time.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="PlayTime API",
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="PlayTime id"
     *   }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerActionBundle:PlayTime:playTimeEditForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/{id}/edit", name="edit", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $play_time = $this->getService()->getOr404($id);
        return $this->createForm(new PlayTimeType(), $play_time, array(
            "action" => $this->generateUrl('api_playtime_put', array('id'=>$id, "access_token"=>$_GET["access_token"])),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a play_time depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="PlayTime API",
     *  statusCodes = {
     *   200 = "Returned when play_time has been successfully deleted.",
     *   404 = "Returned when play_time doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="PlayTime id"
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
        $play_time = $service->getOr404($id);
        if ( isset($play_time) ) {
            return $service->delete($play_time);
        }
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return PlayTimeService
     */
    protected function getService()
    {
        return $this->container->get('action_bundle.play_time.service');
    }

}