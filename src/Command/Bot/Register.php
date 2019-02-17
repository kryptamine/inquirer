<?php

namespace Inquirer\Command\Bot;

use Inquirer\Bridge;
use Inquirer\Entity;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Register extends Command
{
    protected function configure()
    {
        $this
            ->setName('bot:register')
            ->setDescription('Register a new bot')
            ->setHelp('This command allows you to register a new bot.')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the bot.')
            ->addArgument('token', InputArgument::REQUIRED, 'The API token of the bot.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bot = new Entity\Bot($input->getArgument('username'), $input->getArgument('token'));

        $bridge = new Bridge\Bot(
            $bot,
            $this->getSilexApplication()['api']
        );

        try {
            $bridge->register();
        } catch (\Exception $e) {
            $output->writeln("Unable to register bot '{$bot->getUsername()}': {$e->getMessage()}");
        }
    }
}
