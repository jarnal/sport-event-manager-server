<?php
namespace TeamManager\PlayerBundle\Tests\Controller;

use Doctrine\Common\Cache\Cache;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\SecurityBundle\DataFixtures\ORM\LoadOAuthClientData;
use FOS\OAuthServerBundle\Entity\ClientManager;

class MemberControllerTest extends WebTestCase {

    /**
     * Creates the client needed to perform the tests.
     */
    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     * Tests api_player_get_all API method returning all players.
     *
     */
    public function testGetAllAction()
    {
        $player = $this->loadPlayers();
        $access_token = $this->getAccessTokenPlayer( $player->getApiKey() );

        $route =  $this->getUrl('api_player_get_all', array('_format' => 'json', 'access_token'=>$access_token));

        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(isset($result[0]["firstname"]), $content);
    }

    /**
     * Tests api_player_get API method returning a specific player.
     */
    public function testGetAction()
    {
        $player = $this->loadPlayers();
        $access_token = $this->getAccessTokenPlayer( $player->getApiKey() );

        $route = $this->buildGetRoute( $player->getId(), $access_token );
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );
        $this->assertJsonResponse($response, 200);
        $this->assertTrue(isset($result["firstname"]), $content);
    }

    /**
     * Tests api_player_post API with a complete POST Player.
     */
    public function testPostAction()
    {
        $route = $this->buildPostRoute();
        $this->client->request(
            'POST',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"player":{"firstname":"firstname", "username":"foo", "email": "foo@example.org", "password":"hahaha"}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 201, false);
    }

    /**
     * Tests api_player_post API with an incomplete POST Player.
     */
    public function testIncompletePostAction()
    {
        $route = $this->buildPostRoute();
        $this->client->request(
            'POST',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"player":{"username":"foo", "email": "foo@example.org", "password":"hahaha"}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400, false);
    }

    /**
     * Tests the api_player_put with an existing Player and complete Player data.
     */
    public function testJsonPutPageActionShouldModify()
    {
        $player = $this->loadPlayers();
        $accessToken = $this->getAccessTokenPlayer($player->getApiKey());

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildPutRoute($player->getId(), $accessToken);
        $this->client->request(
            'PUT',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"player":{"firstname":"firstname", "username":"foo", "email": "foo@example.org", "password":"hahaha"}}'
        );
    }

    /**
     * Tests the api_player_put API method with a blank Player and complete Player data.
     */
    public function testJsonPutPageActionShouldCreate()
    {
        $player = $this->loadPlayers();
        $accessToken = $this->getAccessTokenPlayer($player->getApiKey());

        $id = 0;
        $route = $this->buildGetRoute($id, $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildPutRoute($id, $accessToken);
        $this->client->request(
            'PUT',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"player":{"firstname":"dff", "username":"dee", "email": "foo@example.org", "password":"hahaha"}}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    /**
     * Tests the api_player_delete API method with an existing Player.
     */
    public function testDeletePageActionShouldDelete()
    {
        $player = $this->loadPlayers();
        $accessToken = $this->getAccessTokenPlayer($player->getApiKey());

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildDeleteRoute($player->getId(), $accessToken);
        $this->client->request(
            'DELETE',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json')
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests the api_player_delete API method with an invalid Player.
     */
    public function testDeletePageActionShouldNotDelete()
    {
        $player = $this->loadPlayers();
        $accessToken = $this->getAccessTokenPlayer($player->getApiKey());

        $id = 0;
        $route = $this->buildGetRoute($id, $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildDeleteRoute($id, $accessToken);
        $this->client->request(
            'DELETE',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json')
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
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
     * Loads players fixtures and returns the added players.
     * Fixtures are loaded only once to have better performances.
     *
     * @return PlayerInterface
     */
    protected function loadPlayers()
    {
        if( is_null(LoadPlayerData::$players) || count(LoadPlayerData::$players)==0 ) {
            $fixtures = array('TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData');
            $this->loadFixtures($fixtures);
        }
        return array_pop(LoadPlayerData::$players);
    }

    /**
     * Builds player get route.
     *
     * @return string
     */
    protected function buildGetRoute($pPlayerID, $pAccessToken)
    {
        return $this->getUrl(
            'api_player_get',
            array(
                'playerID' => $pPlayerID,
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
    protected function buildPostRoute()
    {
        return $route = $this->getUrl( 'api_player_post' , array(
            'access_token'=>$this->getAccessTokenClient(),
            '_format'=>'json'
        ));
    }

    /**
     * Builds player put route.
     *
     * @param $pPlayerID
     * @param $pAccessToken
     * @return string
     */
    protected function buildPutRoute($pPlayerID, $pAccessToken)
    {
        return $this->getUrl( 'api_player_put' , array(
            'access_token'=>$pAccessToken,
            'playerID'=>$pPlayerID,
            '_format'=>'json'
        ));
    }

    /**
     * Builds delete put route.
     *
     * @param $pPlayerID
     * @param $pAccessToken
     * @return string
     */
    protected function buildDeleteRoute($pPlayerID, $pAccessToken)
    {
        return $this->getUrl( 'api_player_delete' , array(
            'access_token'=>$pAccessToken,
            'playerID'=>$pPlayerID,
            '_format'=>'json'
        ));
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
