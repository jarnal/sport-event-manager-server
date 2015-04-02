<?php

namespace TeamManager\ResultBundle\Controller;

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
use TeamManager\ResultBundle\Entity\Comment;
use TeamManager\ResultBundle\Exception\InvalidCommentFormException;
use TeamManager\ResultBundle\Form\CommentType;
use TeamManager\ResultBundle\Service\CommentService;
use TeamManager\CommonBundle\Service\EntityServiceInterface;

class CommentRestController extends FOSRestController
{

    /**
     * Returns all comments.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Comment API",
     *  output={
     *      "class"="TeamManager\ResultBundle\Entity\Comment",
     *      "collection"=true,
     *      "groups"={"CommentSpecific", "PlayerGlobal", "EventMinimal"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "comments"
     *  }
     * )
     *
     * @View(serializerGroups={"CommentSpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/", name="get_all", options={ "method_prefix" = false })
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return array("comments"=>$this->getService()->getAll());
    }

    /**
     * Returns a comment by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Comment API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Comment id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ResultBundle\Entity\Comment",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"CommentSpecific", "PlayerGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when comment exists",
     *     404 = "Returned when the comment is not found"
     *   }
     * )
     *
     * @View(serializerGroups={"CommentSpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/{id}", name="get", options={ "method_prefix" = false })
     *
     * @return Comment
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new comment.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Comment API",
     *  input="TeamManager\ResultBundle\Form\CommentType",
     *  statusCodes = {
     *      200 = "Returned when the comment has been created",
     *      400 = "Returned when the comment form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerResultBundle:Comment:commentForm.html.twig",
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
            $form = new CommentType();
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_comment_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidCommentFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new comment.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Comment API",
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  requirements= {
     *      {
     *          "name"="receiverID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The player id receiving the comment"
     *      },
     *      {
     *          "name"="eventID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Event id related to comment"
     *      }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerResultBundle:Comment:commentForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/receiver/{receiverID}/event/{eventID}/new", name="new", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function newAction(Request $request, $receiverID, $eventID)
    {
        $receiver = $this-> get('player_bundle.player.service')->getOr404($receiverID, false);
        $event = $this->get('event_bundle.event.service')->getOr404($eventID, false);

        $comment = new Comment();
        $comment->setPlayerSender($this->getUser());
        $comment->setPlayerReceiver($receiver);
        $comment->setEvent($event);
        return $this->createForm(
            new CommentType(),
            $comment,
            array(
                "action" => $this->generateUrl('api_comment_post', array("access_token"=>$_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing comment from the submitted data or create a new comment with a specific id.
     *
     * @ApiDoc(
     *   resource = true,
     *   section="Comment API",
     *   input="TeamManager\ResultBundle\Form\CommentType",
     *   statusCodes = {
     *     201 = "Returned when a new comment is created",
     *     204 = "Returned when comment has been updated successfully",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerResultBundle:Comment:commentEditForm.html.twig",
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
            $form = new CommentType();
            if ( !($comment = $service->get($id)) ) {
                $comment = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $comment->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_comment_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $service->put(
                    $comment,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidCommentFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing comment.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Comment API",
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Comment id"
     *   }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerResultBundle:Comment:commentEditForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/{id}/edit", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $comment = $this->getService()->getOr404($id);
        return $this->createForm(new CommentType(), $comment, array(
            "action" => $this->generateUrl('api_comment_put', array('id'=>$id, "access_token"=>$_GET["access_token"])),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a comment depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Comment API",
     *  statusCodes = {
     *   200 = "Returned when comment has been successfully deleted.",
     *   404 = "Returned when comment doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Comment id"
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
        $comment = $service->getOr404($id);
        if ( isset($comment) ) {
            return $service->delete($comment);
        }
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return CommentService
     */
    protected function getService()
    {
        return $this->container->get('result_bundle.comment.service');
    }

}