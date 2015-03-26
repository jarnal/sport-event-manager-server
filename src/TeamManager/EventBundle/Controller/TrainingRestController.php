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
use TeamManager\EventBundle\Entity\Training;
use TeamManager\EventBundle\Exception\InvalidGameFriendlyFormException;
use TeamManager\EventBundle\Exception\InvalidTrainingFormException;
use TeamManager\EventBundle\Form\GameType;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use TeamManager\EventBundle\Form\TrainingType;

class TrainingRestController extends FOSRestController
{

    /**
     * Returns all trainings.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Training API",
     *  output={
     *      "class"="TeamManager\EventBundle\Entity\Training",
     *      "collection"=true,
     *      "groups"={"EventTeam"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "trainings"
     *  }
     * )
     *
     * @View( serializerGroups={"EventTeam"} )
     *
     * @Get("/", name="get_all", options={ "method_prefix" = false })
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return array("trainings"=>$this->getService()->getAll());
    }

    /**
     * Returns a training by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Training API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Training id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\EventBundle\Entity\Training",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"EventDetails", "LocationGlobal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when training exists",
     *     404 = "Returned when the training is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"EventTeam", "LocationGlobal"} )
     *
     * @Get("/{id}", name="get", options={ "method_prefix" = false })
     *
     * @return Training
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new training.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Training API",
     *  input="TeamManager\EventBundle\Form\TrainingType",
     *  statusCodes = {
     *      200 = "Returned when the training has been created",
     *      400 = "Returned when the training form has errors"
     *  }
     * )
     *
     * @View(
     *      serializerGroups={"Default"},
     *      template="TeamManagerTeamBundle:Training:trainingForm.html.twig",
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
            $form = new TrainingType();
            $training = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $training->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_training_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidTrainingFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new friendly game.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Training API",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the related team doesn't exist."
     *  },
     *  requirements={
     *      {
     *          "name"="teamID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The team to which the training will be related."
     *      }
     *  }
     * )
     *
     * @View(
     *      template="TeamManagerEventBundle:Training:trainingForm.html.twig",
     *      templateVar = "form"
     * )
     *
     * @Get("/team/{teamID}/new", name="new", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function newAction(Request $request, $teamID)
    {
        $team = $this->get('team_bundle.team.service')->getOr404($teamID);
        $training = new Training();
        $training->setTeam($team);

        return $this->createForm(
            new TrainingType(),
            $training,
            array(
                "action" => $this->generateUrl('api_training_post', array("access_token" => $_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing training from the submitted data or create a new training.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Training API",
     *  input="TeamManager\EventBundle\Form\TrainingType",
     *  statusCodes = {
     *      201 = "Returned when a new training is created",
     *      204 = "Returned when the training has been updated successfully",
     *      400 = "Returned when the form has errors"
     *  },
     *  requirements= {
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Training id"
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
            $form = new TrainingType();
            $training = $service->get($id);
            if (!$training) {
                $training = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $training->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_training_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $service->put(
                    $training,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidTrainingFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing training.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Training API",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the training doesn't exist."
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Training id"
     *      }
     *  }
     * )
     *
     * @View(
     *      template="TeamManagerEventBundle:Training:trainingEditForm.html.twig",
     *      templateVar = "form"
     * )
     *
     * @Get("/edit/{id}", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $training = $this->getService()->getOr404($id);
        return $this->createForm(new TrainingType(), $training, array(
            "action" => $this->generateUrl('api_training_put', ['id' => $id, 'access_token' => $_GET['access_token']]),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a training depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Training API",
     *  statusCodes = {
     *      200 = "Returned when training has been successfully deleted.",
     *      404 = "Returned when training doesn't exist."
     *  },
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Training id"
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
        $training = $service->getOr404($id);
        if (isset($training)) {
            return $service->delete($training);
        }
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return EntityServiceInterface
     */
    protected function getService()
    {
        return $this->container->get('event_bundle.training.service');
    }

}