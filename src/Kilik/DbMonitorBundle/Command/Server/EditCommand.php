<?php

namespace Kilik\DbMonitorBundle\Command\Server;

use Kilik\DbMonitorBundle\Entity\Server;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EditCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kilik:db-monitor:server:edit')
            ->setDescription('Edit a MySQL server')
            ->addArgument('name', InputArgument::REQUIRED, 'Applicative name')
            ->addOption('host', null,InputOption::VALUE_REQUIRED, 'new Host information (ip or hostname)')
            ->addOption('port', null,InputOption::VALUE_REQUIRED, 'new Port')
            ->addOption('login', null,InputOption::VALUE_REQUIRED, 'new Login')
            ->addOption('password', null,InputOption::VALUE_REQUIRED, 'new Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $server=$em->getRepository('KilikDbMonitorBundle:Server')->findOneByName($input->getArgument('name'));
        if(is_null($server)) {
            $output->writeln('<error>server not found</error>');
            return;
        }

        if($input->getOption('host')) {
            $server->setHost($input->getOption('host'));
        }
        if($input->getOption('port')) {
            $server->setPort($input->getOption('port'));
        }
        if($input->getOption('login')) {
            $server->setLogin($input->getOption('login'));
        }
        if($input->getOption('password')) {
            $server->setPassword($input->getOption('password'));
        }

        $em->persist($server);
        $em->flush();

        $output->writeln('<info>server updated</info>');
    }
}
