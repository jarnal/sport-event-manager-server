<?php
namespace TeamManager\CommonBundle\Tests;

use Doctrine\Common\Cache\Cache;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use FOS\OAuthServerBundle\Entity\ClientManager;

class EntityRestControllerTest extends WebTestCase {

    protected $entityName;

    /**
     * Creates the client needed to perform the tests.
     */
    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     *
     */
    public function testEmptyAction()
    {
        //$this->assertTrue(true);
    }

    /**
     * Initializes test and returns a return token to use calling API methods.
     *
     * @return string
     */
    protected function initializeTest()
    {
        $this->loadDataFixtures();
        $player = $this->getPlayer();
        $access_token = $this->getAccessTokenPlayer($player->getApiKey());

        return $access_token;
    }

    /**
     * Test is returned content is JSON type and corresponds to the passed status code.
     *
     * @param $response
     * @param int $statusCode
     */
    protected function assertJsonResponse($response, $statusCode = 200) {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );

        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    /**
     * @param $pAccessToken
     * @return string
     */
    protected function buildGetAllRoute($pAccessToken)
    {
        return $route =  $this->getUrl(
            sprintf('api_%s_get_all', $this->entityName),
            array(
                '_format' => 'json',
                'access_token'=>$pAccessToken
            )
        );
    }

    /**
     * Builds player get route.
     *
     * @return string
     */
    protected function buildGetRoute($id, $pAccessToken)
    {
        return $this->getUrl(
            sprintf('api_%s_get', $this->entityName),
            array(
                'id' => $id,
                'access_token' => $pAccessToken,
                '_format' => 'json'
            )
        );
    }

    /**
     * Builds player post route.
     *
     * @return string
     */
    protected function buildPostRoute($pAccessToken)
    {
        return $route = $this->getUrl(
            sprintf('api_%s_post', $this->entityName),
            array(
                'access_token'=>$pAccessToken,
                '_format'=>'json'
            )
        );
    }

    /**
     * Builds player put route.
     *
     * @param $id
     * @param $pAccessToken
     * @return string
     */
    protected function buildPutRoute($id, $pAccessToken)
    {
        return $this->getUrl(
            sprintf('api_%s_put', $this->entityName),
            array(
                'access_token'=>$pAccessToken,
                'id'=>$id,
                '_format'=>'json'
            )
        );
    }

    /**
     * Builds delete put route.
     *
     * @param $id
     * @param $pAccessToken
     * @return string
     */
    protected function buildDeleteRoute($id, $pAccessToken)
    {
        return $this->getUrl(
            sprintf('api_%s_delete', $this->entityName),
            array(
                'access_token'=>$pAccessToken,
                'id'=>$id,
                '_format'=>'json'
            )
        );
    }

    /**
     * @return Player
     */
    protected function getPlayer()
    {
        return array_pop(LoadPlayerData::$players);
    }

    /**
     * Builds an access token related to a user.
     *
     * @param $pUserApiKey
     * @return string
     */
    protected function getAccessTokenPlayer($pUserApiKey)
    {
        $grantType = "http://www.teammanager.com/web/app_dev.php/grants/api_key";
        $apiKey = $pUserApiKey;
        return $this->getAccessToken($grantType, $apiKey);
    }

    /**
     * Builds an access token related to the client.
     * Used only for post method because an player can be posted anonymously (register).
     *
     * @return string
     */
    protected function getAccessTokenClient()
    {
        $grantType = "client_credentials";
        return $this->getAccessToken($grantType);
    }

    /**
     * Builds the access token calling OAuth route.
     *
     * @param $pGrantType
     * @param $pApiKey
     * @return string
     */
    protected function getAccessToken($pGrantType, $pApiKey=null)
    {
        $grantTypes = array(
            "http://www.teammanager.com/web/app_dev.php/grants/api_key",
            "client_credentials"
        );

        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris( array() );
        $client->setAllowedGrantTypes( $grantTypes );
        $clientManager->updateClient($client);

        $route =  $this->getUrl('fos_oauth_server_token', array(
            'client_id' => $client->getPublicId(),
            'client_secret' => $client->getSecret(),
            'grant_type' => $pGrantType,
            'api_key' => $pApiKey
        ));

        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );
        $this->assertJsonResponse($response, 200);
        return $result["access_token"];
    }

}
