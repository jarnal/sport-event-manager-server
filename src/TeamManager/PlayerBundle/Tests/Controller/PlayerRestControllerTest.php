<?php
namespace TeamManager\PlayerBundle\Tests\Controller;

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
     * Test is returned content is JSON type.
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
     * Tests API method returning all database players.
     */
    public function testGetAllAction() {
        $expected = '[{"firstname":"TheFirstName1","email":"email1@email.fr"},{"firstname":"TheFirstName2","email":"email2@email.fr"}]';

        $fixtures = array('TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData');
        $this->loadFixtures($fixtures);

        $route =  $this->getUrl('api_player_get_all', array('_format' => 'json'));

        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertJsonResponse($response, 200);
        $this->assertEquals($expected, $content);
    }

    /**
     *
     */
    public function testGetAction()
    {
        $expected = array(
            '{"firstname":"TheFirstName1","email":"email1@email.fr"}',
            '{"firstname":"TheFirstName2","email":"email2@email.fr"}'
        );

        $fixtures = array('TeamManager\PlayerBundle\DataFixtures\ORM\LoadPlayerData');
        $this->loadFixtures($fixtures);
        $players = LoadPlayerData::$players;
        $limit = count($players);

        for($i=0; $i<$limit; $i++) {
            $route =  $this->getUrl('api_player_get', array('playerID' => $players[$i]->getId(), '_format' => 'json'));

            $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
            $response = $this->client->getResponse();
            $content = $response->getContent();

            $this->assertJsonResponse($response, 200);
            $this->assertEquals($expected[$i], $content);
        }
    }

}
