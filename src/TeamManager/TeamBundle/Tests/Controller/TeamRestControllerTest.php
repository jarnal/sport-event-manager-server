<?php
namespace TeamManager\TeamBundle\Tests\Controller;

use Doctrine\Common\Cache\Cache;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\ActionBundle\DataFixtures\ORM\LoadCardData;
use TeamManager\CommonBundle\Tests\EntityRestControllerTest;
use TeamManager\EventBundle\DataFixtures\ORM\LoadGameData;
use TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData;
use TeamManager\PlayerBundle\Entity\PlayerInterface;
use FOS\OAuthServerBundle\Entity\ClientManager;
use TeamManager\TeamBundle\DataFixtures\ORM\LoadTeamData;
use TeamManager\TeamBundle\Entity\Team;

class TeamRestControllerTest extends EntityRestControllerTest {

    /**
     *
     */
    public function __construct()
    {
        $this->entityName = "team";
    }

    /**
     * Tests api_team_get_all API method returning all teams.
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
        foreach($result['teams'] as $team){
            $this->assertTrue(isset($team["name"]), $content);
        }
    }

    /**
     * Tests api_team_get API method returning a specific team.
     */
    public function testGetAction()
    {
        $access_token = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute( $team->getId(), $access_token );
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $result = json_decode( $content, true );
        $this->assertJsonResponse($response, 200);

        $this->assertTrue(isset($result["default_location"]), $content);
    }

    /**
     * Tests api_team_post API with a complete POST team.
     */
    public function testPostAction()
    {
        $access_token = $this->initializeTest();
        $player = $this->getPlayer();

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
     * Tests api_team_post API with an incomplete POST team.
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
            '{"team":{"name":"LaTeam", "default_location":1}}'
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400, false);
    }

    /**
     * Tests the api_team_put with an existing team and complete team data.
     */
    public function testJsonPutPageActionShouldModify()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildPutRoute($team->getId(), $accessToken);
        $this->client->request(
            'PUT',
            $route,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"team":{"name":"LaTeamChanged"}}'
        );
        $response = $this->client->getResponse();

        $this->assertTrue($response->getStatusCode() == 204);
    }

    /**
     * Tests the api_team_put API method with a blank team and complete team data.
     */
    public function testJsonPutPageActionShouldCreate()
    {
        $accessToken = $this->initializeTest();
        $player = $this->getPlayer();

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
            '{"team":{"name":"LaTeam", "default_location":1, "manager":'.$player->getId().'}}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    /**
     * Tests the api_team_delete API method with an existing team.
     */
    public function testDeletePageActionShouldDelete()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->buildDeleteRoute($team->getId(), $accessToken);
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
     * Tests the api_team_delete API method with an invalid team.
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
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_events', array('access_token'=>$accessToken, 'id'=>$team->getId(), '_format'=>'json'));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['events'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue(isset($event['season']));
        }
    }

    /**
     * Tests the api_team_events_season API method returning all events for a team in a season.
     */
    public function testListEventsBySeason()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $season = '2014-2015';

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_events_season', array(
            'teamID'=>$team->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['events'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue(isset($event['season']));
        }
    }

    /**
     * Tests the api_team_games API method returning all games for a team.
     */
    public function testListGames()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_games', array('access_token'=>$accessToken, 'id'=>$team->getId(), '_format'=>'json'));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['games'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue($event['type'] == 'game');
        }
    }

    /**
     * Tests the api_team_games_season API method returning all games for a team in a season.
     */
    public function testListGamesBySeason()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $season = '2014-2015';

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_games_season', array(
            'teamID'=>$team->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['games'] as $event)
        {
            $this->assertTrue(isset($event['season']));
            $this->assertTrue($event['season'] == $season);
            $this->assertTrue($event['type'] == 'game');
        }
    }

    /**
     * Tests the api_team_friendly_games API method returning all friendly games for a team.
     */
    public function testListFriendlyGames()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_friendly_games', array('access_token'=>$accessToken, 'id'=>$team->getId(), '_format'=>'json'));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['friendly_games'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue($event['type'] == 'game_friendly');
        }
    }

    /**
     * Tests the api_team_friendly_games API method returning all friendly games for a team in a season.
     */
    public function testListFriendlyGamesBySeason()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $season = '2014-2015';

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_friendly_games_season', array(
            'teamID'=>$team->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['friendly_games'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue($event['season'] == $season);
            $this->assertTrue($event['type'] == 'game_friendly');
        }
    }

    /**
     * Tests the api_team_trainings API method returning all trainings for a team.
     */
    public function testListTrainings()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_trainings', array('access_token'=>$accessToken, 'id'=>$team->getId(), '_format'=>'json'));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['trainings'] as $event)
        {
            $this->assertTrue(isset($event['name']));
            $this->assertTrue(isset($event['location']));
            $this->assertTrue($event['type'] == 'training');
        }
    }

    /**
     * Tests the api_team_trainings API method returning all trainings for a team in a season.
     */
    public function testListTrainingsBySeason()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $season = '2014-2015';

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_trainings_season', array(
            'teamID'=>$team->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['trainings'] as $event)
        {
            $this->assertTrue($event['season'] == $season);
            $this->assertTrue($event['type'] == 'training');
        }
    }

    /**
     * Tests the api_team_cards API method returning all cards for a team.
     */
    public function testListCards()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_cards', array(
            'id'=>$team->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['cards'] as $card)
        {
            $this->assertTrue(isset($card['type']));
            $this->assertTrue(isset($card['player']));
        }
    }

    /**
     * Tests the api_team_cards_season API method returning all cards for a team in a season.
     */
    public function testListCardsBySeason()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $season = '2014-2015';

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_cards_season', array(
            'teamID'=>$team->getId(),
            'season'=>$season,
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['cards'] as $card)
        {
            $this->assertTrue(isset($card['type']));
            $this->assertTrue(isset($card['player']));
        }
    }

    /**
     * Tests the api_team_game_cards API method returning all cards for a team in a specific game.
     */
    public function testListCardsForGame()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $game = $this->fixtures->getReference('game-1');

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_game_cards', array(
            'teamID'=>$team->getId(),
            'gameID'=>$game->getId(),
            'access_token'=>$accessToken,
            '_format'=>'json'
        ));
        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $result = json_decode($content, true);

        $this->assertJsonResponse($response, 200);
        foreach($result['cards'] as $card)
        {
            $this->assertTrue(isset($card['type']));
            $this->assertTrue(isset($card['player']));
        }
    }

    /**
     * Tests the api_team_goals API method returning all goals for a team.
     */
    public function testListGoals()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_goals', array(
            'id'=>$team->getId(),
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
     * Tests the api_team_goals_season API method returning all goals for a team in a season.
     */
    public function testListGoalsBySeason()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $season = '2013-2014';

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_goals_season', array(
            'teamID'=>$team->getId(),
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
     * Tests the api_team_game_goals API method returning all goals for a team in a specific game.
     */
    public function testListGoalsForGame()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $game = $this->fixtures->getReference('game-1');

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_game_goals', array(
            'teamID'=>$team->getId(),
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
     * Tests the api_team_injuries API method returning all injuries for a team.
     */
    public function testListInjuries()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_injuries', array(
            'id'=>$team->getId(),
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
     * Tests the api_team_injuries_season API method returning all injuries for a team in a season.
     */
    public function testListInjuriesBySeason()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $season = '2013-2014';

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_injuries_season', array(
            'teamID'=>$team->getId(),
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
     * Tests the api_team_game_injuries API method returning all injuries for a team in a specific game.
     */
    public function testListInjuriesForGame()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $game = $this->fixtures->getReference('game-1');

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_game_injuries', array(
            'teamID'=>$team->getId(),
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
     * Tests the api_team_play_times API method returning all play_times for a team.
     */
    public function testListPlayTimes()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_play_times', array(
            'id'=>$team->getId(),
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
     * Tests the api_team_play_times_season API method returning all play_times for a team in a season.
     */
    public function testListPlayTimesBySeason()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $season = '2013-2014';

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_play_times_season', array(
            'teamID'=>$team->getId(),
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
     * Tests the api_team_game_play_times API method returning all play_times for a team in a specific game.
     */
    public function testListPlayTimesForGame()
    {
        $accessToken = $this->initializeTest();
        $team = $this->getTeam();
        $game = $this->fixtures->getReference('game-1');

        $route = $this->buildGetRoute($team->getId(), $accessToken);
        $this->client->request(
            'GET',
            $route,
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $route = $this->getUrl('api_team_game_play_times', array(
            'teamID'=>$team->getId(),
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
            'TeamManager\ActionBundle\DataFixtures\ORM\LoadPlayTimeData'
        );
        return $fixtures;
    }

    /**
     * Returns a random team loaded by fixtures.
     *
     * @return Team
     */
    protected function getTeam()
    {
        return $this->fixtures->getReference('team-1');
    }

}
