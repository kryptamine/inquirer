<?php

namespace Inquirer\Command\Bot;

use Inquirer\Exception\StorageException;
use Inquirer\Factory\Bot;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GetList
 * @package Inquirer\Command\Bot
 */
class GetList extends Command
{
    protected function configure()
    {
        $this
            ->setName('bot:list')
            ->setDescription('Display list of registered bots')
            ->setHelp('This command allows you to see a list of bot.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bridge = new Bot(
            $this->getSilexApplication()['botStorage']
        );

        try {
            foreach ($bridge->getList() as $bot) {
                $output->writeln("{$bot->getUsername()}: {$bot->getToken()}");
            }
        } catch (StorageException $e) {
            $output->writeln("Unable to display a list of registered bots: {$e->getMessage()}");
        }
    }
}
