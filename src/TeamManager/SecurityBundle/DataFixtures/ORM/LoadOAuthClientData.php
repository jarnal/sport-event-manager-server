<?php
namespace TeamManager\SecurityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TeamManager\PlayerBundle\Entity\Player;
use TeamManager\SecurityBundle\Entity\Client;

class LoadOAuthClientData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var Client
     */
    static public $client;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();

        $client = new Client();
        $client->setRandomId('3k9xvu0iql2ckcs4kwksgo8gkccoco0gg8kcwkgcg4g4c8s8sg');
        $client->setRedirectUris('a:0:{}');
        $client->setSecret('3ndsmfw42mo0ckcc0w4woww8ww00o4kk48o0wg4sgcwkk0s40s');
        $client->setAllowedGrantTypes('a:1:{i:0;s:57:"http://www.teammanager.com/web/app_dev.php/grants/api_key";}');

        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://www.example.com'));
        $client->setAllowedGrantTypes(array('token', 'authorization_code'));
        $clientManager->updateClient($client);

        $manager->persist($client);
        $manager->flush();

        static::$client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}