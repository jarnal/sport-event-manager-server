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
use TeamManager\ActionBundle\Entity\Card;
use TeamManager\ActionBundle\Exception\InvalidCardFormException;
use TeamManager\ActionBundle\Form\CardType;
use TeamManager\ActionBundle\Service\CardService;
use TeamManager\CommonBundle\Service\EntityServiceInterface;

class CardRestController extends FOSRestController
{

    /**
     * Returns all cards.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Card API",
     *  output={
     *      "class"="TeamManager\ActionBundle\Entity\Card",
     *      "collection"=true,
     *      "groups"={"Default"},
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *          "Nelmio\ApiDocBundle\Parser\CollectionParser"
     *      },
     *      "collectionName" = "cards"
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
        return array("cards"=>$this->getService()->getAll());
    }

    /**
     * Returns a card by id.
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Card API",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Card id"
     *      }
     *  },
     *  output={
     *      "class"="\TeamManager\ActionBundle\Entity\Card",
     *      "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"={"Default"}
     *  },
     *  statusCodes = {
     *     200 = "Returned when card exists",
     *     404 = "Returned when the card is not found"
     *   }
     * )
     *
     * @View( serializerGroups={"Default"} )
     *
     * @Get("/{id}", name="get", options={ "method_prefix" = false })
     *
     * @return Card
     */
    public function getAction($id)
    {
        return $this->getService()->getOr404($id);
    }

    /**
     * Adds a new card.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Card API",
     *  input="TeamManager\ActionBundle\Form\CardType",
     *  statusCodes = {
     *      200 = "Returned when the card has been created",
     *      400 = "Returned when the card form has errors"
     *  }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerActionBundle:Card:cardForm.html.twig",
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
            $form = new CardType($this->getUser());
            $player = $this->getService()->post(
                $request->request->get($form->getName()),
                $this->getUser()
            );

            $routeOptions = array(
                'id' => $player->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_team_get', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidCardFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to create a new card.
     *
     * @ApiDoc(
     *   resource = true,
     *   section="Card API",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @View(
     *  template="TeamManagerActionBundle:Card:cardForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/new/player/{playerID}/game/{gameID}", name="new", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function newAction(Request $request, $playerID, $gameID)
    {
        $player = $this-> get('player_bundle.player.service')->getOr404($playerID);
        $game = $this->get('event_bundle.game.service')->getOr404($gameID);

        $card = new Card();
        $card->setPlayer($player);
        $card->setGame($game);
        return $this->createForm(
            new CardType(),
            $card,
            array(
                "action" => $this->generateUrl('api_card_post', array("access_token"=>$_GET["access_token"])),
                "method" => "POST"
            )
        );
    }

    /**
     * Update existing card from the submitted data or create a new card with a specific id.
     *
     * @ApiDoc(
     *   resource = true,
     *   section="Card API",
     *   input="TeamManager\ActionBundle\Form\CardType",
     *   statusCodes = {
     *     201 = "Returned when a new card is created",
     *     204 = "Returned when card has been updated successfully",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @View(
     *  serializerGroups={"Default"},
     *  template="TeamManagerActionBundle:Card:cardEditForm.html.twig",
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
            $form = new CardType();
            if ( !($card = $service->get($id)) ) {
                $card = $service->post(
                    $request->request->get($form->getName())
                );

                $routeOptions = array(
                    'id' => $card->getId(),
                    '_format' => $request->get('_format')
                );

                return $this->routeRedirectView('api_card_get', $routeOptions, Codes::HTTP_CREATED);
            } else {
                $service->put(
                    $card,
                    $request->request->get($form->getName())
                );

                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (InvalidCardFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Builds the form to use to update an existing card.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Card API",
     *  statusCodes = {
     *   200 = "Returned when successful"
     *  },
     *  requirements={
     *  {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Card id"
     *   }
     *  }
     * )
     *
     * @View(
     *  template="TeamManagerActionBundle:Card:cardEditForm.html.twig",
     *  templateVar = "form"
     * )
     *
     * @Get("/edit/{id}", name="edit", options={ "method_prefix" = false })
     *
     * @return FormTypeInterface
     */
    public function editAction($id)
    {
        $card = $this->getService()->getOr404($id);
        return $this->createForm(new CardType(), $card, array(
            "action" => $this->generateUrl( 'api_card_put' , ['id'=>$id] ),
            "method" => "PUT"
        ));
    }

    /**
     * Deletes a card depending on the passed id.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Card API",
     *  statusCodes = {
     *   200 = "Returned when card has been successfully deleted.",
     *   404 = "Returned when card doesn't exist."
     *  },
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="integer",
     *    "requirement"="\d+",
     *    "description"="Card id"
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
        $card = $service->getOr404($id);
        if ( isset($card) ) {
            return $service->delete($card);
        }
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return CardService
     */
    protected function getService()
    {
        return $this->container->get('action_bundle.card.service');
    }

}