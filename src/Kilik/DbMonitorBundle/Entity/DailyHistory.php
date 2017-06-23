<?php

namespace Kilik\DbMonitorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kilik\DbMonitorBundle\Entity\Traits\DateTrait;

/**
 * DailyHistory
 *
 * @ORM\Table(name="daily_history",
 *     uniqueConstraints={
 *     @ORM\UniqueConstraint(name="uniq_idx", columns={"id_server","db_name","table_name","date"}),
 *     })
 * @ORM\Entity(repositoryClass="Kilik\DbMonitorBundle\Repository\DailyHistoryRepository")
 */
class DailyHistory extends AbstractHistory
{
    use DateTrait;
}

