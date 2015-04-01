<?php
namespace TeamManager\PlayerBundle\Tests\Controller;

use Doctrine\Common\Cache\Cache;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\CommonBundle\Tests\EntityRestControllerTest;
use TeamManager\EventBundle\DataFixtures\ORM\LoadGameData;
use TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use FOS\OAuthServerBundle\Entity\ClientManager;

class PlayerRestControllerTest extends EntityRestControllerTest {

    /**
     *
     */
    public function __construct()
    {
        $this->entityName = "player";
    }

    /**
     * Tests api_player_get_all API method returning all players.
     *
     */
    public function testGetAllAction()
    {
        $accessToken = $this->initializeTest();

        $route =  $this->getUrl('api_player_get_all', array('_format' => 'json', 'access_token'=>$accessToken));

        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );

        $this->assertJsonResponse($response, 200);
        foreach($result['players'] as $player){
            $this->assertTrue(isset($player["firstname"]), $content);
            $this->assertTrue(isset($player["lastname"]), $content);
        }
    }

    /**
     * Tests api_player_get API method returning a specific player.
     */
    public function testGetAction()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
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
        $accessToken = $this->initializeTest();

        $route = $this->buildPostRoute($accessToken);
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
        $accessToken = $this->initializeTest();

        $route = $this->buildPostRoute($accessToken);
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
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

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
            '{"player":{"firstname":"firstname"}}'
        );
        $response = $this->client->getResponse();

        $this->assertTrue($response->getStatusCode() == 204);
    }

    /**
     * Tests the api_player_put API method with a blank Player and complete Player data.
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
            '{"player":{"firstname":"dff", "username":"dee", "email": "foo@example.org", "password":"hahaha"}}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    /**
     * Tests the api_player_delete API method with an existing Player.
     */
    public function testDeletePageActionShouldDelete()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

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
     * Tests the api_team_events API method returning all events for a team.
     */
    public function testListEvents()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_events', array('access_token'=>$accessToken, 'id'=>$player->getId(), '_format'=>'json'));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result['events'])>0);
        foreach($result['events'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue(isset($event['season']));
            $this->assertTrue(isset($event['team']));
        }
    }

    /**
     * Tests the api_player_events_season API method returning all events for a team in a season.
     */
    public function testListEventsBySeason()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $season = '2013-2014';

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_events_season', array(
            'playerID'=>$player->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result['events'])>0);
        foreach($result['events'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue(isset($event['season']));
            $this->assertTrue(isset($event['team']));
        }
    }

    /**
     * Tests the api_player_games API method returning all games for a team.
     */
    public function testListGames()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_games', array('access_token'=>$accessToken, 'id'=>$player->getId(), '_format'=>'json'));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result['games'])>0);
        foreach($result['games'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue($event['type'] == 'game');
            $this->assertTrue(isset($event['team']));
        }
    }

    /**
     * Tests the api_player_games_season API method returning all games for a team in a season.
     */
    public function testListGamesBySeason()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $season = '2013-2014';

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_games_season', array(
            'playerID'=>$player->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result['games'])>0);
        foreach($result['games'] as $event)
        {
            $this->assertTrue(isset($event['season']));
            $this->assertTrue($event['season'] == $season);
            $this->assertTrue($event['type'] == 'game');
            $this->assertTrue(isset($event['team']));
        }
    }

    /**
     * Tests the api_player_friendly_games API method returning all friendly games for a team.
     */
    public function testListFriendlyGames()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_friendly_games', array('access_token'=>$accessToken, 'id'=>$player->getId(), '_format'=>'json'));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result['friendly_games'])>0);
        foreach($result['friendly_games'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue($event['type'] == 'game_friendly');
            $this->assertTrue(isset($event['team']));
        }
    }

    /**
     * Tests the api_player_friendly_games API method returning all friendly games for a team in a season.
     */
    public function testListFriendlyGamesBySeason()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $season = '2013-2014';

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_friendly_games_season', array(
            'playerID'=>$player->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result['friendly_games'])>0);
        foreach($result['friendly_games'] as $event)
        {
            $this->assertTrue($event['season'] == $season);
            $this->assertTrue($event['type'] == 'game_friendly');
            $this->assertTrue(isset($event['team']));
        }
    }

    /**
     * Tests the api_player_trainings API method returning all trainings for a team.
     */
    public function testListTrainings()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_trainings', array('access_token'=>$accessToken, 'id'=>$player->getId(), '_format'=>'json'));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["trainings"])>0);
        foreach($result['trainings'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue($event['type'] == 'training');
            $this->assertTrue(isset($event['team']));
        }
    }

    /**
     * Tests the api_player_trainings API method returning all trainings for a team in a season.
     */
    public function testListTrainingsBySeason()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $season = '2013-2014';

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_trainings_season', array(
            'playerID'=>$player->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["trainings"])>0);
        foreach($result['trainings'] as $event)
        {
            $this->assertTrue($event['season'] == $season);
            $this->assertTrue($event['type'] == 'training');
            $this->assertTrue(isset($event['team']));
        }
    }

    /**
     * Tests the api_player_cards API method returning all cards for a team.
     */
    public function testListCards()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_cards', array(
            'id'=>$player->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["cards"])>0);
        foreach($result['cards'] as $card)
        {
            $this->assertTrue(isset($card['type']));
            $this->assertTrue(isset($card['game']));
        }
    }

    /**
     * Tests the api_player_cards_season API method returning all cards for a team in a season.
     */
    public function testListCardsBySeason()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $season = '2013-2014';

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_cards_season', array(
            'playerID'=>$player->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["cards"])>0);
        foreach($result['cards'] as $card)
        {
            $this->assertTrue(isset($card['type']));
            $this->assertTrue(isset($card['game']));
        }
    }

    /**
     * Tests the api_player_game_cards API method returning all cards for a team in a specific game.
     */
    public function testListCardsForGame()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $game = LoadGameData::$games[0];

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_game_cards', array(
            'playerID'=>$player->getId(),
            'gameID'=>$game->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["cards"])>0);
        foreach($result['cards'] as $card)
        {
            $this->assertTrue(isset($card['type']));
            $this->assertTrue(isset($card['game']));
        }
    }

    /**
     * Tests the api_player_goals API method returning all goals for a team.
     */
    public function testListGoals()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_goals', array(
            'id'=>$player->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["goals"])>0);
        foreach($result['goals'] as $goal)
        {
            $this->assertTrue(isset($goal['type']));
            $this->assertTrue(isset($goal['game']));
        }
    }

    /**
     * Tests the api_player_goals_season API method returning all goals for a team in a season.
     */
    public function testListGoalsBySeason()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $season = '2013-2014';

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_goals_season', array(
            'playerID'=>$player->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["goals"])>0);
        foreach($result['goals'] as $goal)
        {
            $this->assertTrue(isset($goal['type']));
            $this->assertTrue(isset($goal['game']));
        }
    }

    /**
     * Tests the api_player_game_goals API method returning all goals for a team in a specific game.
     */
    public function testListGoalsForGame()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $game = LoadGameData::$games[0];

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_game_goals', array(
            'playerID'=>$player->getId(),
            'gameID'=>$game->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["goals"])>0);
        foreach($result['goals'] as $goal)
        {
            $this->assertTrue(isset($goal['type']));
            $this->assertTrue(isset($goal['game']));
        }
    }

    /**
     * Tests the api_player_injuries API method returning all injuries for a team.
     */
    public function testListInjuries()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_injuries', array(
            'id'=>$player->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["injuries"])>0);
        foreach($result['injuries'] as $injury)
        {
            $this->assertTrue(isset($injury['type']));
            $this->assertTrue(isset($injury['game']));
        }
    }

    /**
     * Tests the api_player_injuries_season API method returning all injuries for a team in a season.
     */
    public function testListInjuriesBySeason()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $season = '2013-2014';

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_injuries_season', array(
            'playerID'=>$player->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["injuries"])>0);
        foreach($result['injuries'] as $injury)
        {
            $this->assertTrue(isset($injury['type']));
            $this->assertTrue(isset($injury['game']));
        }
    }

    /**
     * Tests the api_player_game_injuries API method returning all injuries for a team in a specific game.
     */
    public function testListInjuriesForGame()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $game = LoadGameData::$games[0];

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_game_injuries', array(
            'playerID'=>$player->getId(),
            'gameID'=>$game->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["injuries"])>0);
        foreach($result['injuries'] as $injury)
        {
            $this->assertTrue(isset($injury['type']));
            $this->assertTrue(isset($injury['game']));
        }
    }

    /**
     * Tests the api_player_play_times API method returning all play_times for a team.
     */
    public function testListPlayTimes()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_play_times', array(
            'id'=>$player->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["play_times"])>0);
        foreach($result['play_times'] as $play_time)
        {
            $this->assertTrue(isset($play_time['duration']));
            $this->assertTrue(isset($play_time['game']));
        }
    }

    /**
     * Tests the api_player_play_times_season API method returning all play_times for a team in a season.
     */
    public function testListPlayTimesBySeason()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $season = '2013-2014';

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_play_times_season', array(
            'playerID'=>$player->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["play_times"])>0);
        foreach($result['play_times'] as $play_time)
        {
            $this->assertTrue(isset($play_time['duration']));
            $this->assertTrue(isset($play_time['game']));
        }
    }

    /**
     * Tests the api_player_game_play_times API method returning all play_times for a team in a specific game.
     */
    public function testListPlayTimesForGame()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();
        $game = LoadGameData::$games[0];

        $route = $this->buildGetRoute($player->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_player_game_play_times', array(
            'playerID'=>$player->getId(),
            'gameID'=>$game->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        $this->assertTrue(count($result["play_times"])>0);
        foreach($result['play_times'] as $play_time)
        {
            $this->assertTrue(isset($play_time['duration']));
            $this->assertTrue(isset($play_time['game']));
        }
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
            'TeamManager\EventBundle\DataFixtures\ORM\LoadGameFriendlyData',
            'TeamManager\EventBundle\DataFixtures\ORM\LoadTrainingData',
            'TeamManager\ActionBundle\DataFixtures\ORM\LoadCardData',
            'TeamManager\ActionBundle\DataFixtures\ORM\LoadGoalData',
            'TeamManager\ActionBundle\DataFixtures\ORM\LoadInjuryData',
            'TeamManager\ActionBundle\DataFixtures\ORM\LoadPlayTimeData',
        );
        $this->loadFixtures($fixtures);
    }

}
