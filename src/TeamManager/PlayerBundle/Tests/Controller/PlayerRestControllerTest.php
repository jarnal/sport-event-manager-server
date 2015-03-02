<?php
namespace TeamManager\PlayerBundle\Tests\Controller;

use Doctrine\Common\Cache\Cache;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData;

class MemberControllerTest extends WebTestCase {

    /**
     * Creates the client needed to perform the tests.
     */
    public function setUp(){
        $this->client = static::createClient();
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
     * Tests api_player_get_all API method returning all players.
     *
     */
    public function testGetAllAction()
    {
        $fixtures = array('TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData');
        $this->loadFixtures($fixtures);

        $route =  $this->getUrl('api_player_get_all', array('_format' => 'json'));

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
        $fixtures = array('TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData');
        $this->loadFixtures($fixtures);
        $player = array_pop(LoadPlayerData::$players);

        $route =  $this->getUrl('api_player_get', array('playerID' => $player->getId(), '_format' => 'json'));

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
        $route = $this->getUrl( 'api_player_post' , array(
            '_format'=>'json'
        ));

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
        $route = $this->getUrl( 'api_player_post' , array(
            '_format'=>'json'
        ));

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
        $fixtures = array('TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData');
        $this->loadFixtures($fixtures);
        $player = array_pop(LoadPlayerData::$players);

        $this->client->request(
            'GET',
            sprintf('/api/player/get/%d.json', $player->getId()),
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl( 'api_player_put' , array(
            'playerID'=>$player->getId(),
            '_format'=>'json'
        ));
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
        $id = 0;
        $this->client->request(
            'GET',
            sprintf('/api/player/get/%d.json', $id),
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl( 'api_player_put' , array(
            'playerID'=>$id,
            '_format'=>'json'
        ));
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
        $fixtures = array('TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData');
        $this->loadFixtures($fixtures);
        $player = array_pop(LoadPlayerData::$players);

        $this->client->request(
            'GET',
            sprintf('/api/player/get/%d.json', $player->getId()),
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl( 'api_player_delete' , array(
            'playerID'=>$player->getId(),
            '_format'=>'json'
        ));
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
        $id = 0;
        $this->client->request(
            'GET',
            sprintf('/api/player/get/%d.json', $id),
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl( 'api_player_delete' , array(
            'playerID'=>$id,
            '_format'=>'json'
        ));
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

}
