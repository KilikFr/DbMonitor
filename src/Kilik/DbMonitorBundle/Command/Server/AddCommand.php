<?php

namespace Kilik\DbMonitorBundle\Command\Server;

use Kilik\DbMonitorBundle\Entity\Server;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kilik:db-monitor:server:add')
            ->setDescription('Add a MySQL server')
            ->addArgument('name', InputArgument::REQUIRED, 'Applicative name')
            ->addArgument('host', InputArgument::REQUIRED, 'Host information (ip or hostname)')
            ->addArgument('port', InputArgument::REQUIRED, 'Port number')
            ->addArgument('login', InputArgument::REQUIRED, 'Login')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = new Server();
        $server->setName($input->getArgument('name'));
        $server->setHost($input->getArgument('host'));
        $server->setPort($input->getArgument('port'));
        $server->setLogin($input->getArgument('login'));
        $server->setPassword($input->getArgument('password'));
        $server->setDisabled(false);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($server);
        $em->flush();

        $output->writeln('<info>server added</info>');
    }
}
