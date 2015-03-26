<?php
namespace TeamManager\ActionBundle\Tests\Controller;

use Doctrine\Common\Cache\Cache;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\ActionBundle\DataFixtures\ORM\LoadCardData;
use TeamManager\CommonBundle\Tests\EntityRestControllerTest;
use TeamManager\EventBundle\DataFixtures\ORM\LoadGameData;
use TeamManager\EventBundle\Entity\Game;
use TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use FOS\OAuthServerBundle\Entity\ClientManager;
use TeamManager\TeamBundle\DataFixtures\ORM\LoadTeamData;
use TeamManager\TeamBundle\Entity\Team;

class CardRestControllerTest extends EntityRestControllerTest {

    /**
     *
     */
    public function __construct()
    {
        $this->entityName = "card";
    }

    /**
     * Tests api_card_get_all API method returning all teams.
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
        foreach($result['cards'] as $cards){
            $this->assertTrue(isset($cards["type"]), $content);
            $this->assertTrue(isset($cards["player"]), $content);
            $this->assertTrue(isset($cards["game"]), $content);
        }
    }

    /**
     * Tests api_card_get API method returning a specific team.
     */
    public function testGetAction()
    {
        $access_token = $this->initializeTest();
        $card = $this->getCard();

        $route = $this->buildGetRoute($card->getId(), $access_token);
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );
        $this->assertJsonResponse($response, 200);

        $this->assertTrue(isset($result["type"]), $content);
        $this->assertTrue(isset($result["player"]), $content);
        $this->assertTrue(isset($result["game"]), $content);
    }

    /**
     * Tests api_card_post API with a complete POST team.
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
            '{"card":{"type":"Card.YELLOW_CARD", "player":'.$this->getPlayer()->getId().', "game":'.$this->getGame()->getId().'}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 201, false);
    }

    /**
     * Tests api_card_post API with an incomplete POST team.
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
            '{"card":{"type":"Pouet"}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400, false);
    }

    /**
     * Tests api_card_post API with an incomplete POST team.
     */
    public function testIncorrectPlayerPostAction()
    {
        $access_token = $this->initializeTest();

        $player = LoadPlayerData::$players[20];

        $route = $this->buildPostRoute($access_token);
        $this->client->request(
            'POST',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"card":{"type":"Card.YELLOW_CARD", "player":'.$player->getId().', "game":'.$this->getGame()->getId().'}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400, false);
    }

    /**
     * Tests the api_card_put with an existing team and complete team data.
     */
    public function testJsonPutPageActionShouldModify()
    {
        $accessToken = $this->initializeTest();
        $card = $this->getCard();

        $route = $this->buildGetRoute($card->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildPutRoute($card->getId(), $accessToken);
        $this->client->request(
            'PUT',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"card":{"type":"Card.RED_CARD"}}'
        );
        $response = $this->client->getResponse();

        $this->assertTrue($response->getStatusCode() == 204);
    }

    /**
     * Tests the api_card_put API method with a blank team and complete team data.
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
            '{"card":{"type":"Card.YELLOW_CARD", "player":'.$this->getPlayer()->getId().', "game":'.$this->getGame()->getId().'}}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    /**
     * Tests the api_card_delete API method with an existing team.
     */
    public function testDeletePageActionShouldDelete()
    {
        $accessToken = $this->initializeTest();
        $card = $this->getCard();

        $route = $this->buildGetRoute($card->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildDeleteRoute($card->getId(), $accessToken);
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
     * Tests the api_card_delete API method with an invalid team.
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
            'TeamManager\ActionBundle\DataFixtures\ORM\LoadCardData'
        );
        $this->loadFixtures($fixtures);
    }

    /**
     * Returns a random game loaded by fixtures.
     *
     * @return Game
     */
    protected function getCard()
    {
        return LoadCardData::$cards[0];
    }

    /**
     * Returns a random player loaded by fixtures.
     *
     * @return Team
     */
    protected function getPlayer()
    {
        return LoadPlayerData::$players[0];
    }

    /**
     * Returns a random player loaded by fixtures.
     *
     * @return Team
     */
    protected function getGame()
    {
        return LoadGameData::$games[0];
    }

}
