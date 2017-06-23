<?php

namespace Kilik\DbMonitorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HourlyHistory.
 *
 * @ORM\Table(name="hourly_history",
 *     uniqueConstraints={
 *     @ORM\UniqueConstraint(name="uniq_idx", columns={"id_server","db_name","table_name","date"}),
 *     })
 * @ORM\Entity(repositoryClass="Kilik\DbMonitorBundle\Repository\HourlyHistoryRepository")
 */
class HourlyHistory extends AbstractHistory
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return static
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
