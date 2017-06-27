<?php

namespace Kilik\DbMonitorBundle\Controller;

use Kilik\DbMonitorBundle\Entity\Server;
use Kilik\TableBundle\Components\Column;
use Kilik\TableBundle\Components\Filter;
use Kilik\TableBundle\Components\Table;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EvolutionController.
 *
 * @Route("/evolution")
 */
class EvolutionController extends AbstractController
{
    /**
     * Check evolution on one hour.
     *
     * @Route("/{history}/{server}/{date}", name="kilik_db_monitor_evolution_history")
     * @Template()
     * @ParamConverter("date", options={"format": "Y-m-d_H:i"})
     *
     * @param string    $history
     * @param Server    $server
     * @param \DateTime $date
     *
     * @return array
     */
    public function historyAction($history, Server $server, \DateTime $date)
    {
        $data = [
            'server' => $server,
            'history' => $history,
            'date' => $date,
            'table' => $this->get('kilik_table')->createFormView($this->getTable($history, $server, $date)),
        ];

        return $data;
    }

    /**
     * Global list, by server.
     *
     * @param string    $history
     * @param Server    $server
     * @param \DateTime $date
     *
     * @return Table
     */
    private function getTable($history, Server $server, \DateTime $date)
    {
        /* SELECT h.db_name,h.table_name,
    h.nb_rows AS nb_rows, ph.nb_rows AS ph_nb_rows
    FROM hourly_history AS h
    LEFT JOIN hourly_history AS ph ON ph.db_name=h.db_name AND ph.table_name = h.table_name AND ph.id_server=h.id_server AND ph.date = '2017-06-26 15:00:00'
    WHERE h.date = '2017-06-26 16:00:00'
        */

        $previousDate = clone $date;
        switch ($history) {
            case 'monthly':
                $repositoryName = 'KilikDbMonitorBundle:MonthlyHistory';
                $previousDate->modify('-1 month');
                break;
            case 'daily':
                $repositoryName = 'KilikDbMonitorBundle:DailyHistory';
                $previousDate->modify('-1 day');
                break;
            case 'hourly':
            default:
                $repositoryName = 'KilikDbMonitorBundle:HourlyHistory';
                $previousDate->modify('-1 hour');
                break;
        }

        $queryBuilder = $this->manager()
            ->getRepository($repositoryName)
            ->createQueryBuilder('h')
            ->select('h,h.dataLength AS dataLength,h.nbRows AS nbRows,ph.nbRows AS previousNbRows,ph.dataLength AS previousDataLength,100*ROUND((h.nbRows-ph.nbRows)/ph.nbRows,2) AS ratioRows,100*ROUND((h.dataLength-ph.dataLength)/ph.dataLength,2) AS ratioDataLength,(h.dataLength-ph.dataLength) AS deltaLength')
            ->leftJoin($repositoryName, 'ph', 'WITH', 'h.server = ph.server AND h.dbName = ph.dbName AND h.tableName=ph.tableName AND ph.date = :previousDate')
            ->where('h.server = :server AND h.date = :date')
            ->setParameter('server', $server)
            ->setParameter('previousDate', $previousDate)
            ->setParameter('date', $date);

        $table = (new Table())
            ->setRowsPerPage(10)
            ->setId('kilik_db_monitor_evolution_history_list')
            ->setPath($this->generateUrl('kilik_db_monitor_evolution_history_list_ajax', ['history' => $history, 'server' => $server->getId(), 'date' => $date->format('Y-m-d_H:i')]))
            ->setTemplate('KilikDbMonitorBundle:Evolution:_historyList.html.twig')
            ->setTemplateParams(['history' => $history, 'date' => $date, 'server' => $server])
            ->setQueryBuilder($queryBuilder, 'h');

        $table->addColumn(
            (new Column())->setLabel('Database')
                ->setSort(['h.dbName' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('h.dbName')
                        ->setName('h_dbName')
                )
        );

        $table->addColumn(
            (new Column())->setLabel('Table')
                ->setSort(['h.tableName' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('h.tableName')
                        ->setName('h_tableName')
                )
        );

        $table->addColumn(
            (new Column())->setLabel('Nb rows')
                ->setSort(['nbRows' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('nbRows')
                        ->setName('nbRows')
                        ->setHaving(true)
                )
        );

        $table->addColumn(
            (new Column())->setLabel('Prev. rows')
                ->setSort(['previousNbRows' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('previousNbRows')
                        ->setName('previousNbRows')
                        ->setHaving(true)
                )
        );

        $table->addColumn(
            (new Column())->setLabel('% rows')
                ->setSort(['ratioRows' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('ratioRows')
                        ->setName('ratioRows')
                        ->setHaving(true)
                )
        );

        $table->addColumn(
            (new Column())->setLabel('Data Length')
                ->setSort(['dataLength' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('dataLength')
                        ->setName('dataLength')
                        ->setHaving(true)
                )
        );

        $table->addColumn(
            (new Column())->setLabel('Prev. dataLength')
                ->setSort(['previousDataLength' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('previousDataLength')
                        ->setName('previousDataLength')
                        ->setHaving(true)
                )
        );

        $table->addColumn(
            (new Column())->setLabel('% data')
                ->setSort(['ratioDataLength' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('ratioDataLength')
                        ->setName('ratioDataLength')
                        ->setHaving(true)
                )
        );

        $table->addColumn(
            (new Column())->setLabel('delta data')
                ->setSort(['deltaLength' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('deltaLength')
                        ->setName('deltaLength')
                        ->setHaving(true)
                )
        );

        return $table;
    }

    /**
     * @Route("/{history}/{server}/{date}/ajax", name="kilik_db_monitor_evolution_history_list_ajax")
     * @Template()
     * @ParamConverter("date", options={"format": "Y-m-d_H:i"})
     *
     * @param string    $history
     * @param Server    $server
     * @param \DateTime $date
     *
     * @return Response
     */
    public function _listAction(Request $request, $history, Server $server, \DateTime $date)
    {
        return $this->get('kilik_table')->handleRequest($this->getTable($history, $server, $date), $request);
    }
}