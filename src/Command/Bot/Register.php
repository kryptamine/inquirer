<?php

namespace Inquirer\Command\Bot;

use Inquirer\Service\BotService;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Register
 * @package Inquirer\Command\Bot
 */
class Register extends Command
{
    protected function configure()
    {
        $this
            ->setName('bot:register')
            ->setDescription('Register a new bot')
            ->setHelp('This command allows you to register a new bot.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the bot.')
            ->addArgument('token', InputArgument::REQUIRED, 'The API token of the bot.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $botService = new BotService($this->getSilexApplication()['api'], $this->getSilexApplication()['botStorage']);

        try {
            $botService->register($input->getArgument('username'), $input->getArgument('token'));
        } catch (\Exception $e) {
            $output->writeln("Unable to register bot '{$input->getArgument('username')}': {$e->getMessage()}");
        }
    }
}
