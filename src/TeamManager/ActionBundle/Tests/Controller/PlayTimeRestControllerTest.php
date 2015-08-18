<?php
namespace TeamManager\ActionBundle\Tests\Controller;

use Doctrine\Common\Cache\Cache;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\ActionBundle\DataFixtures\ORM\LoadCardData;
use TeamManager\ActionBundle\DataFixtures\ORM\LoadInjuryData;
use TeamManager\ActionBundle\DataFixtures\ORM\LoadPlayTimeData;
use TeamManager\CommonBundle\Tests\EntityRestControllerTest;
use TeamManager\EventBundle\DataFixtures\ORM\LoadGameData;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use FOS\OAuthServerBundle\Entity\ClientManager;
use TeamManager\TeamBundle\DataFixtures\ORM\LoadTeamData;
use TeamManager\TeamBundle\Entity\Team;

class PlayTimeRestControllerTest extends EntityRestControllerTest {

    /**
     *
     */
    public function __construct()
    {
        $this->entityName = "playtime";
    }

    /**
     * Tests api_injury_get_all API method returning all teams.
     *
     */
    public function testGetAllAction()
    {
        $access_token = $this->initializeTest();

        $route =  $this->buildGetAllRoute($access_token);

        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );
        $this->assertJsonResponse($response, 200);
        foreach($result['play_times'] as $injuries){
            $this->assertTrue(isset($injuries["duration"]), $content);
            $this->assertTrue(isset($injuries["player"]), $content);
            $this->assertTrue(isset($injuries["game"]), $content);
        }
    }

    /**
     * Tests api_injury_get API method returning a specific team.
     */
    public function testGetAction()
    {
        $access_token = $this->initializeTest();
        $injury = $this->getPlayTime();

        $route = $this->buildGetRoute($injury->getId(), $access_token);
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );
        $this->assertJsonResponse($response, 200);
        $this->assertTrue(isset($result["duration"]), $content);
        $this->assertTrue(isset($result["player"]), $content);
        $this->assertTrue(isset($result["game"]), $content);
    }

    /**
     * Tests api_injury_post API with a complete POST team.
     */
    public function testPostAction()
    {
        $access_token = $this->initializeTest();

        $route = $this->buildPostRoute($access_token);
        $this->client->request(
            'POST',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"play_time":{"duration":"45", "player":'.$this->getPlayer()->getId().', "game":'.$this->getGame()->getId().'}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 201, false);
    }

    /**
     * Tests api_injury_post API with an incomplete POST team.
     */
    public function testIncompletePostAction()
    {
        $access_token = $this->initializeTest();

        $route = $this->buildPostRoute($access_token);
        $this->client->request(
            'POST',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"play_time":{"duration":"45"}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400, false);
    }

    /**
     * Tests api_injury_post API with an user not expected or present in the game related to the injury.
     */
    public function testIncorrectPlayerPostAction()
    {
        $access_token = $this->initializeTest();

        $player = $this->fixtures->getReference('player-20');

        $route = $this->buildPostRoute($access_token);
        $this->client->request(
            'POST',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"play_time":{"duration":"45", "player":'.$player->getId().', "game":'.$this->getGame()->getId().'}}'
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400, false);
    }

    /**
     * Tests the api_injury_put with an existing team and complete team data.
     */
    public function testJsonPutPageActionShouldModify()
    {
        $accessToken = $this->initializeTest();
        $injury = $this->getPlayTime();

        $route = $this->buildGetRoute($injury->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildPutRoute($injury->getId(), $accessToken);
        $this->client->request(
            'PUT',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"play_time":{"duration":"45"}}'
        );
        $response = $this->client->getResponse();

        $this->assertTrue($response->getStatusCode() == 204);
    }

    /**
     * Tests the api_injury_put API method with a blank team and complete team data.
     */
    public function testJsonPutPageActionShouldCreate()
    {
        $accessToken = $this->initializeTest();

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
            '{"play_time":{"duration":"45", "player":'.$this->getPlayer()->getId().', "game":'.$this->getGame()->getId().'}}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    /**
     * Tests the api_injury_delete API method with an existing team.
     */
    public function testDeletePageActionShouldDelete()
    {
        $accessToken = $this->initializeTest();
        $injury = $this->getPlayTime();

        $route = $this->buildGetRoute($injury->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildDeleteRoute($injury->getId(), $accessToken);
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
     * Tests the api_injury_delete API method with an invalid team.
     */
    public function testDeletePageActionShouldNotDelete()
    {
        $accessToken = $this->initializeTest();

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
     * Loads all needed fixtures.
     */
    protected function loadDataFixtures()
    {
        $fixtures = array(
            'TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData',
            'TeamManager\TeamBundle\DataFixtures\ORM\LoadTeamData',
            'TeamManager\EventBundle\DataFixtures\ORM\LoadGameData',
            'TeamManager\ActionBundle\DataFixtures\ORM\LoadPlayTimeData'
        );
        return $fixtures;
    }

    /**
     * Returns a random game loaded by fixtures.
     *
     * @return Game
     */
    protected function getPlayTime()
    {
        return $this->fixtures->getReference('playTime-1');
    }

    /**
     * Returns a random player loaded by fixtures.
     *
     * @return Team
     */
    protected function getPlayer()
    {
        return $this->fixtures->getReference('player-1');
    }

    /**
     * Returns a random player loaded by fixtures.
     *
     * @return Team
     */
    protected function getGame()
    {
        return $this->fixtures->getReference('game-1');
    }

}
