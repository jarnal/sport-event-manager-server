<?php

namespace TeamManager\SecurityBundle\Controller;

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
use FOS\RestBundle\Controller\Annotations\Link;
use FOS\RestBundle\Controller\Annotations\Unlink;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Exception\Exception;
use TeamManager\CommonBundle\Service\EntityServiceInterface;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\Entity\Player;
use FOS\RestBundle\Controller\Annotations\View;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\PlayerBundle\Exception\InvalidUserFormException;
use TeamManager\PlayerBundle\Form\PlayerType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class SecurityController
 * @package TeamManager\SecurityBundle\Controller
 */
class SecurityRestController extends FOSRestController
{

    /**
     * Before client can start calling the API, a user has to be logged in the app.
     * This methods returns, if the user credentials are right, the unique api_key for this user.
     * This way is more secure than passing an id, because it's easier to guess an id than a long string like this one.
     *
     * @Route("/api/private/credentials")
     */

    /**
     * Adds a new player.
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Security API",
     *  statusCodes = {
     *      200 = "Returned when the player has been created",
     *      400 = "Returned when the player form has errors"
     *  }
     * )
     *
     * @View( serializerGroups={"PlayerAPIKey"} )
     *
     * @Post("/security/" , name="post", options={ "method_prefix" = false })
     *
     * @return|View
     *
     */
    public function credentialsAction( Request $request )
    {
        $user_login = $request->request->get('user_l');
        $user_pwd = $request->request->get('user_p');

        //RETURN API
        return $this->getService()->getByLoginPasswordOr404($user_login, $user_pwd);

        /*$em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('HinnoyaLMSUserBundle:User')->findByUsername( $user_login )[0];*/

        //If no user exists with this login, return false.
        /*if( !$user ){
            $response = new Response( json_encode( array( "error"=>"No user for this login ".$user_login , "code" => 0 ) ) );
            return $response;
        }*/

        //If user exists but password doesn't match, return false.
        /*/if( $user->getPassword() != $user_pwd ){
            $response = new Response( json_encode( array( "error"=>"Password doesn't match" , "code" => 50 ) ) );
            return $response;
        }

        $arr = array( "api_key"=>$user->getApiKey() , "student_id"=>$user->getId() , "code"=>100 );
        $response = new JSONResponse( $arr );
        return $response;*/
    }

    /**
     * Returns the appropriate service to handle related entity.
     *
     * @return EntityServiceInterface
     */
    protected function getService()
    {
        return $this->container->get('player_bundle.player.service');
    }

}
