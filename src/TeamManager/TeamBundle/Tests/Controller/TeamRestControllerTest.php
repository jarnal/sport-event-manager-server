<?php
namespace TeamManager\TeamBundle\Tests\Controller;

use Doctrine\Common\Cache\Cache;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\CommonBundle\Tests\EntityRestControllerTest;
use TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use TeamManager\SecurityBundle\DataFixtures\ORM\LoadOAuthClientData;
use FOS\OAuthServerBundle\Entity\ClientManager;
use TeamManager\TeamBundle\DataFixtures\ORM\LoadTeamData;

class TeamRestControllerTest extends EntityRestControllerTest {

    /**
     *
     */
    public function __construct()
    {
        $this->entityName = "team";
    }

    /**
     * Tests api_player_get_all API method returning all players.
     *
     */
    public function testGetAllAction()
    {
        $this->loadDataFixtures();
        $access_token = $this->getAccessTokenPlayer( $this->getApiKey() );

        $route =  $this->buildGetAllRoute($access_token);

        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );
        $this->assertJsonResponse($response, 200);
        $this->assertTrue(isset($result[0]["default_location"]), $content);
    }

    /**
     * Tests api_player_get API method returning a specific player.
     */
    public function testGetAction()
    {
        $player = $this->loadDataFixtures();
        $access_token = $this->getAccessTokenPlayer( $this->getApiKey() );

        $route = $this->buildGetRoute( $player->getId(), $access_token );
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );
        $this->assertJsonResponse($response, 200);

        $this->assertTrue(isset($result["default_location"]), $content);
    }

    /**
     * Tests api_player_post API with a complete POST Player.
     */
    public function testPostAction()
    {
        $player = $this->loadDataFixtures();
        $access_token = $this->getAccessTokenPlayer( $this->getApiKey() );

        $route = $this->buildPostRoute($access_token);
        $this->client->request(
            'POST',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"team":{"name":"LaTeam", "default_location":1, "manager":'.$player->getId().'}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 201, false);
    }

    /**
     * Tests api_player_post API with an incomplete POST Player.
     */
    public function testIncompletePostAction()
    {
        /*$route = $this->buildPostRoute();
        $this->client->request(
            'POST',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"player":{"username":"foo", "email": "foo@example.org", "password":"hahaha"}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400, false);*/
    }

    /**
     * Tests the api_player_put with an existing Player and complete Player data.
     */
    public function testJsonPutPageActionShouldModify()
    {
        /*$player = $this->loadDataFixtures();
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
        );*/
    }

    /**
     * Tests the api_player_put API method with a blank Player and complete Player data.
     */
    public function testJsonPutPageActionShouldCreate()
    {
        /*$player = $this->loadDataFixtures();
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

        $this->assertJsonResponse($this->client->getResponse(), 201, false);*/
    }

    /**
     * Tests the api_player_delete API method with an existing Player.
     */
    public function testDeletePageActionShouldDelete()
    {
        /*$player = $this->loadDataFixtures();
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
        $this->assertJsonResponse($response, 200);*/
    }

    /**
     * Tests the api_player_delete API method with an invalid Player.
     */
    public function testDeletePageActionShouldNotDelete()
    {
        /*$player = $this->loadDataFixtures();
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
        $this->assertJsonResponse($response, 404);*/
    }

    /**
     * Loads teams fixtures and returns the added teams.
     * Fixtures are loaded only once to have better performances.
     *
     * @return PlayerInterface
     */
    protected function loadDataFixtures()
    {
        if( is_null(LoadTeamData::$teams) || count(LoadTeamData::$teams)==0 ) {
            $fixtures = array(
                'TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData',
                'TeamManager\TeamBundle\DataFixtures\ORM\LoadTeamData'
            );
            $this->loadFixtures($fixtures);
        }
        return array_pop(LoadTeamData::$teams);
    }

}
