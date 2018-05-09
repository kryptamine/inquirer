<?php

namespace Inquirer\Command\Bot;

use Inquirer\Bridge;
use Inquirer\Exception\StorageException;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bridge = new Bridge\Bot(
            $this->getSilexApplication()['botStorage'],
            $this->getSilexApplication()['api']
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
