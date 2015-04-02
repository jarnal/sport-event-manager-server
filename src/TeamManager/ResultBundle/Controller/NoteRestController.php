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
use TeamManager\ResultBundle\Entity\Note;
use TeamManager\ResultBundle\Exception\InvalidNoteFormException;
use TeamManager\ResultBundle\Form\NoteType;
use TeamManager\ResultBundle\Service\NoteService;
use TeamManager\CommonBundle\Service\EntityServiceInterface;

class NoteRestController extends FOSRestController
{

    /**
     * Returns all notes.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Note API",
     *  output={
     *      "class"="TeamManager\ResultBundle\Entity\Note",
     *      "collection"=true,
     *      "groups"={"NoteSpecific", "PlayerGlobal", "EventMinimal"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "notes"
     *  }
     * )
     *
     * @View(serializerGroups={"NoteSpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/", name="get_all", options={ "method_prefix" = false })
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        return array("notes"=>$this->getService()->getAll());
    }

    /**
     * Returns a note by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Note API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Note id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ResultBundle\Entity\Note",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"NoteSpecific", "PlayerGlobal", "EventMinimal"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when note exists",
     *     404 = "Returned when the note is not found"
     *   }
     * )
     *
     * @View(serializerGroups={"NoteSpecific", "PlayerGlobal", "EventMinimal"})
     *
     * @Get("/{id}", name="get", options={ "method_prefix" = false })
     *
     * @return Note
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new note.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Note API",
     *  input="TeamManager\ResultBundle\Form\NoteType",
     *  statusCodes = {
     *      200 = "Returned when the note has been created",
     *      400 = "Returned when the note form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerResultBundle:Note:noteForm.html.twig",
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
            $form = new NoteType();
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_note_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidNoteFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new note.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Note API",
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  requirements= {
     *      {
     *          "name"="receiverID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The player id receiving the note"
     *      },
     *      {
     *          "name"="eventID",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Event id related to note"
     *      }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerResultBundle:Note:noteForm.html.twig",
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

        $note = new Note();
        $note->setPlayerSender($this->getUser());
        $note->setPlayerReceiver($receiver);
        $note->setEvent($event);
        return $this->createForm(
            new NoteType(),
            $note,
            array(
                "action" => $this->generateUrl('api_note_post', array("access_token"=>$_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing note from the submitted data or create a new note with a specific id.
     *
     * @ApiDoc(
     *   resource = true,
     *   section="Note API",
     *   input="TeamManager\ResultBundle\Form\NoteType",
     *   statusCodes = {
     *     201 = "Returned when a new note is created",
     *     204 = "Returned when note has been updated successfully",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerResultBundle:Note:noteEditForm.html.twig",
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
            $form = new NoteType();
            if ( !($note = $service->get($id)) ) {
                $note = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $note->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_note_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $service->put(
                    $note,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidNoteFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing note.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Note API",
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Note id"
     *   }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerResultBundle:Note:noteEditForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/{id}/edit", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $note = $this->getService()->getOr404($id);
        return $this->createForm(new NoteType(), $note, array(
            "action" => $this->generateUrl('api_note_put', array('id'=>$id, "access_token"=>$_GET["access_token"])),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a note depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Note API",
     *  statusCodes = {
     *   200 = "Returned when note has been successfully deleted.",
     *   404 = "Returned when note doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Note id"
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
        $note = $service->getOr404($id);
        if ( isset($note) ) {
            return $service->delete($note);
        }
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return NoteService
     */
    protected function getService()
    {
        return $this->container->get('result_bundle.note.service');
    }

}