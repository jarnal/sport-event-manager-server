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