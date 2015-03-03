<?php

namespace TeamManager\PlayerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamManager\PlayerBundle\Entity\Player;

/**
 * Class CreateClientCommand
 * @package TeamManager\PlayerBundle\Command
 */
class CreatePlayerCommand extends ContainerAwareCommand
{

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('tm:player:create')
            ->setDescription('Creates a new player')
            ->addOption(
                'firstname',
                null,
                InputOption::VALUE_REQUIRED,
                'Sets firstname for the player',
                null
            )
            ->addOption(
                'username',
                null,
                InputOption::VALUE_REQUIRED,
                'Sets username for the player',
                null
            )
            ->addOption(
                'email',
                null,
                InputOption::VALUE_REQUIRED,
                'Sets email for the player',
                null
            )
            ->addOption(
                'password',
                null,
                InputOption::VALUE_REQUIRED,
                'Sets username for the player',
                null
            );
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( "Adding new Player" );

        $player = new Player();
        $player->setFirstname($input->getOption('firstname'));
        $player->setUsername($input->getOption('username'));
        $player->setEmail($input->getOption('email'));

        $factory = $this->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($player);
        $password = $encoder->encodePassword($input->getOption('password'), $player->getSalt() );
        $player->setPassword( $password );

        $apiKey = $encoder->encodePassword($password, $player->getSalt() );
        $player->setApiKey($apiKey);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($player);
        $em->flush();

        $output->writeln( $password );
    }
}

//www.teammanager.com/web/app_dev.php/oauth/v2/token?client_id=3_3k9xvu0iql2ckcs4kwksgo8gkccoco0gg8kcwkgcg4g4c8s8sg&client_secret=3ndsmfw42mo0ckcc0w4woww8ww00o4kk48o0wg4sgcwkk0s40s&grant_type=http://www.teammanager.com/web/app_dev.php/grants/api_key&api_key=04792dbbdb1d05c2a81c1d0a53bc267c0fb8cd70