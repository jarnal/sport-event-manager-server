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
use TeamManager\ActionBundle\Entity\Goal;
use TeamManager\ActionBundle\Exception\InvalidGoalFormException;
use TeamManager\ActionBundle\Form\GoalType;
use TeamManager\ActionBundle\Service\GoalService;
use TeamManager\CommonBundle\Service\EntityServiceInterface;

class GoalRestController extends FOSRestController
{

    /**
     * Returns all goals.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Goal API",
     *  output={
     *      "class"="TeamManager\ActionBundle\Entity\Goal",
     *      "collection"=true,
     *      "groups"={"GoalSpecific", "PlayerGlobal", "EventMinimal"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "goals"
     *  }
     * )
     *
     * @View(serializerGroups={"GoalSpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/", name="get_all", options={"method_prefix" = false})
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return array("goals"=>$this->getService()->getAll());
    }

    /**
     * Returns a goal by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Goal API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Goal id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Goal",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"GoalSpecific", "PlayerGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when goal exists",
     *     404 = "Returned when the goal is not found"
     *   }
     * )
     *
     * @View(serializerGroups={"GoalSpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/{id}", name="get", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return Goal
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new goal.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Goal API",
     *  input="TeamManager\ActionBundle\Form\GoalType",
     *  statusCodes = {
     *      200 = "Returned when the goal has been created",
     *      400 = "Returned when the goal form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerActionBundle:Goal:goalForm.html.twig",
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
            $form = new GoalType($this->getUser());
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_team_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidGoalFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new goal.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Goal API",
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
     *          "description"="Related goal id"
     *      }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerActionBundle:Goal:goalForm.html.twig",
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

        $goal = new Goal();
        $goal->setPlayer($player);
        $goal->setGame($game);
        return $this->createForm(
            new GoalType(),
            $goal,
            array(
                "action" => $this->generateUrl('api_goal_post', array("access_token"=>$_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing goal from the submitted data or create a new goal with a specific id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Goal API",
     *  input="TeamManager\ActionBundle\Form\GoalType",
     *  statusCodes = {
     *      201 = "Returned when a new goal is created",
     *      204 = "Returned when goal has been updated successfully",
     *      400 = "Returned when the form has errors"
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Goal id"
     *      }
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerActionBundle:Goal:goalEditForm.html.twig",
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
            $form = new GoalType();
            if ( !($goal = $service->get($id)) ) {
                $goal = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $goal->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_goal_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $service->put(
                    $goal,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidGoalFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing goal.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Goal API",
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Goal id"
     *   }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerActionBundle:Goal:goalEditForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/{id}/edit", name="edit", options={"method_prefix" = false}, requirements={"id"="\d+"})
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $goal = $this->getService()->getOr404($id);
        return $this->createForm(new GoalType(), $goal, array(
            "action" => $this->generateUrl('api_goal_put', array('id'=>$id, "access_token"=>$_GET["access_token"])),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a goal depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Goal API",
     *  statusCodes = {
     *   200 = "Returned when goal has been successfully deleted.",
     *   404 = "Returned when goal doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Goal id"
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
        $goal = $service->getOr404($id);
        if ( isset($goal) ) {
            return $service->delete($goal);
        }
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return GoalService
     */
    protected function getService()
    {
        return $this->container->get('action_bundle.goal.service');
    }

}