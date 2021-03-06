<?php

namespace Kilik\DbMonitorBundle\Repository;

use Kilik\DbMonitorBundle\Entity\AbstractHistory;
use Kilik\DbMonitorBundle\Entity\Server;

/**
 * HistoryRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AbstractHistoryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get max Date history.
     *
     * @return \DateTime|null
     */
    public function maxDate()
    {
        $qb = $this->createQueryBuilder('h')->select('h')->orderBy('h.date', 'DESC')->setMaxResults(1);
        /** @var AbstractHistory $last */
        $last = $qb->getQuery()->getOneOrNullResult();

        if (is_null($last)) {
            return;
        }

        return $last->getDate();
    }

    /**
     * Count databases.
     *
     * @param \DateTime $date
     *
     * @return int
     */
    public function countDatabases(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('count(distinct(h.dbName)) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count databases by server.
     *
     * @param \DateTime $date
     * @param Server    $server
     *
     * @return int
     */
    public function countDatabasesByServer(\DateTime $date, Server $server)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('count(distinct(h.dbName)) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->andWhere('h.server = :server')
            ->setParameter('server', $server)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count tables.
     *
     * @param \DateTime $date
     *
     * @return int
     */
    public function countTables(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('count(distinct(h.tableName)) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count tables by server.
     *
     * @param \DateTime $date
     * @param Server    $server
     *
     * @return int
     */
    public function countTablesByServer(\DateTime $date, Server $server)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('count(distinct(h.tableName)) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->andWhere('h.server = :server')
            ->setParameter('server', $server)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count rows.
     *
     * @param \DateTime $date
     *
     * @return int
     */
    public function countRows(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('sum(h.nbRows) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count rows by server.
     *
     * @param \DateTime $date
     * @param Server    $server
     *
     * @return int
     */
    public function countRowsByServer(\DateTime $date, Server $server)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('sum(h.nbRows) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->andWhere('h.server = :server')
            ->setParameter('server', $server)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count data length.
     *
     * @param \DateTime $date
     *
     * @return int
     */
    public function countDataLength(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('sum(h.dataLength) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count data length by server.
     *
     * @param \DateTime $date
     * @param Server    $server
     *
     * @return int
     */
    public function countDataLengthByServer(\DateTime $date, Server $server)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('sum(h.dataLength) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->andWhere('h.server = :server')
            ->setParameter('server', $server)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count index length.
     *
     * @param \DateTime $date
     *
     * @return int
     */
    public function countIndexLength(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('sum(h.indexLength) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }

    /**
     * Count index length by server.
     *
     * @param \DateTime $date
     * @param Server    $server
     *
     * @return int
     */
    public function countIndexLengthByServer(\DateTime $date, Server $server)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('sum(h.indexLength) AS nb')
            ->where('h.date = :date')
            ->setParameter('date', $date)
            ->andWhere('h.server = :server')
            ->setParameter('server', $server)
            ->groupBy('h.date');

        $row = $qb->getQuery()->getOneOrNullResult();

        if (is_null($row)) {
            return;
        }

        return $row['nb'];
    }
}
