<?php

// src/Acme/DemoBundle/OAuth/ApiKeyGrantExtension.php

namespace TeamManager\SecurityBundle\OAuth;

use Doctrine\Common\Persistence\ObjectRepository;
use FOS\OAuthServerBundle\Storage\GrantExtensionInterface;
use OAuth2\Model\IOAuth2Client;

/**
 * Check if an user can get an access token with an Api Key.
 */
class ApiKeyGrantExtension implements GrantExtensionInterface
{

    private $playerRepository;

    /**
     * @param ObjectRepository $playerRepository
     */
    public function __construct(ObjectRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /*
     * {@inheritdoc}
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {
        $player = $this->playerRepository->findOneBy( array("api_key"=>$inputData['api_key']) );

        if ($player) {
            //if you need to return access token with associated user
            return array(
                'data' => $player
            );

            //if you need an anonymous user token
            return true;
        }

        return false;
    }
}