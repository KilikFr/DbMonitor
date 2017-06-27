<?php

namespace Kilik\DbMonitorBundle\Controller;

use Kilik\TableBundle\Components\Column;
use Kilik\TableBundle\Components\Filter;
use Kilik\TableBundle\Components\Table;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $lastTime = $this->manager()->getRepository('KilikDbMonitorBundle:HourlyHistory')->maxDate();

        $dataLength = $this->manager()->getRepository('KilikDbMonitorBundle:HourlyHistory')->countDataLength($lastTime);
        $indexLength = $this->manager()->getRepository('KilikDbMonitorBundle:HourlyHistory')->countIndexLength($lastTime);

        $data = [
            'lastTime' => $lastTime,
            'nbServers' => $this->manager()->getRepository('KilikDbMonitorBundle:Server')->count(),
            'nbDatabases' => $this->manager()->getRepository('KilikDbMonitorBundle:HourlyHistory')->countDatabases($lastTime),
            'nbTables' => $this->manager()->getRepository('KilikDbMonitorBundle:HourlyHistory')->countTables($lastTime),
            'nbRows' => $this->manager()->getRepository('KilikDbMonitorBundle:HourlyHistory')->countRows($lastTime),
            'dataLength' => $dataLength,
            'indexLength' => $indexLength,
            'totalLength' => $indexLength + $dataLength,
            'table' => $this->get('kilik_table')->createFormView($this->getDashboardTable($lastTime)),
        ];

        return $data;
    }

    /**
     * Global list, by server.
     *
     * @param \DateTime $date
     *
     * @return Table
     */
    private function getDashboardTable(\DateTime $date)
    {
        $queryBuilder = $this->manager()
            ->getRepository('KilikDbMonitorBundle:Server')
            ->createQueryBuilder('s')
            ->select('s,COUNT(DISTINCT(h.dbName)) AS nbDatabases,COUNT(DISTINCT(h.tableName)) AS nbTables,ROUND(SUM(h.dataLength)/1000000) AS dataLength')
            ->join('KilikDbMonitorBundle:HourlyHistory', 'h', 's = h.server')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->groupBy('s');

        $table = (new Table())
            ->setRowsPerPage(10)
            ->setId('kilik_db_monitor_dashboard_list')
            ->setPath($this->generateUrl('dashboard_list_ajax', ['lastTime' => $date->format('Y-m-d H:i')]))
            ->setTemplate('KilikDbMonitorBundle:Default:_dashboardList.html.twig')
            ->setTemplateParams(['lastTime' => $date])
            ->setQueryBuilder($queryBuilder, 's')
            ->addColumn(
                (new Column())->setLabel('Server')
                    ->setSort(['s.name' => 'asc'])
                    ->setFilter(
                        (new Filter())
                            ->setField('s.name')
                            ->setName('s_name')
                    )
            );

        $table->addColumn(
            (new Column())->setLabel('Nb Databases')
                ->setSort(['nbDatabases' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('nbDatabases')
                        ->setName('nbDatabases')
                        ->setHaving(true)
                )
        );

        $table->addColumn(
            (new Column())->setLabel('Nb Tables')
                ->setSort(['nbTables' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('nbTables')
                        ->setName('nbTables')
                        ->setHaving(true)
                )
        );

        $table->addColumn(
            (new Column())->setLabel('Datalength (Mo)')
                ->setSort(['dataLength' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('dataLength')
                        ->setName('dataLength')
                        ->setHaving(true)
                )
        );

        return $table;
    }

    /**
     * @Route("/dashboard-list/{lastTime}/ajax", name="dashboard_list_ajax")
     * @ParamConverter("lastTime", options={"format": "Y-m-d H:i"})
     */
    public function _listAction(Request $request, \DateTime $lastTime)
    {
        return $this->get('kilik_table')->handleRequest($this->getDashboardTable($lastTime), $request);
    }
}
