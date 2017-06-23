<?php

namespace Kilik\DbMonitorBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Kilik\DbMonitorBundle\Entity\AbstractHistory;
use Kilik\DbMonitorBundle\Entity\DailyHistory;
use Kilik\DbMonitorBundle\Entity\HourlyHistory;
use Kilik\DbMonitorBundle\Entity\MonthlyHistory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScanCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('kilik:db-monitor:scan')
            ->setDescription('Scan databases for stats')
            //->addArgument('servers', InputArgument::OPTIONAL, 'Limit to this server')
            ->addOption('server', null, InputOption::VALUE_REQUIRED, 'Servers to scan', 'all')
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'History mode', 'hourly')
            ->addOption('auto-purge', null, InputOption::VALUE_NONE, 'Remove old stats');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = time();

        /** @var Connection $dbal */
        $dbal = $this->getContainer()->get('doctrine')->getConnection();
        $em = $this->getContainer()->get('doctrine')->getManager();
        $servers = $em->getRepository('KilikDbMonitorBundle:Server')->findBy(['disabled' => false], ['name' => 'ASC']);

        $connectionFactory = $this->getContainer()->get('doctrine.dbal.connection_factory');

        foreach ($servers as $server) {
            if ($input->getOption('server') == 'all' || in_array($server->getName(), explode(',', $input->getOption('server')))) {
                try {
                    $connection = $connectionFactory->createConnection(
                        [
                            'driver' => 'pdo_mysql',
                            'user' => $server->getLogin(),
                            'password' => $server->getPassword(),
                            'host' => $server->getHost(),
                            'port' => $server->getPort(),
                            'dbname' => 'information_schema',
                        ]
                    );

                    $stmt = $connection->executeQuery('SELECT * FROM TABLES');
                    $rows = $stmt->fetchAll();
                    $output->writeln('<info>scanning '.$server->getName().' ('.count($rows).' tables)</info>');
                    foreach ($rows as $row) {
                        switch ($input->getOption('mode')) {
                            case 'daily':
                                $history = new DailyHistory();
                                $history->setDate(new \DateTime(date('Y-m-d 00:00:00', $time)));
                                break;
                            case 'monthly':
                                $history = new MonthlyHistory();
                                $history->setDate(new \DateTime(date('Y-m-01 00:00:00', $time)));
                                break;
                            case 'hourly':
                            default:
                                $history = new HourlyHistory();
                                $history->setDate(new \DateTime(date('Y-m-d H:00:00', $time)));
                                break;
                        }

                        /* @var AbstractHistory $history */
                        $history->setServer($server);
                        $history->setDbName($row['TABLE_SCHEMA']);
                        $history->setTableName($row['TABLE_NAME']);
                        $history->setNbRows($row['TABLE_ROWS'] + 0);
                        $history->setDataLength($row['DATA_LENGTH'] + 0);
                        $history->setIndexLength($row['INDEX_LENGTH'] + 0);
                        $em->persist($history);
                    }
                } catch (ConnectionException $e) {
                    $output->writeln('<error>can\'t connect to '.$server->getName().'</error>');
                }
            }
            try {
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $output->writeln('<error>duplicate entry detected, don\'t scan more than one time by period.</error>');
            }
            $em->clear('Kilik\DbMonitorBundle\Entity\HourlyHistory');
            $em->clear('Kilik\DbMonitorBundle\Entity\DailyHistory');
            $em->clear('Kilik\DbMonitorBundle\Entity\MonthlyHistory');
        }

        // auto remove old data ?
        if ($input->hasOption('auto-purge') || true) {
            switch ($input->getOption('mode')) {
                case 'monthly':
                    // keep 61 months
                    $date = (new \DateTime())->modify('-61 months');
                    $qb = $dbal->createQueryBuilder()
                        ->delete('monthly_history')
                        ->where('date < :date')
                        ->setParameter('date', $date->format('Y-m-01 00:00:00'));
                    break;
                case 'daily':
                    // keep 2 months (62 days)
                    $date = (new \DateTime())->modify('-62 days');
                    $qb = $dbal->createQueryBuilder()
                        ->delete('daily_history')
                        ->where('date < :date')
                        ->setParameter('date', $date->format('Y-m-d 00:00:00'));
                    break;
                case 'hourly':
                default:
                    // keep 49 hours (2 days)
                    $date = (new \DateTime())->modify('-49 hours');
                    $qb = $dbal->createQueryBuilder()
                        ->delete('hourly_history')
                        ->where('date < :date')
                        ->setParameter('date', $date->format('Y-m-d H:00:00'));
            }
            $affectedRows=$qb->execute();
            $output->writeln('<info>delete '.$input->getOption('mode').' records before '.$date->format('Y-m-d H:i').': '.$affectedRows.' rows deleted</info>');
        }
    }
}
