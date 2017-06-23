<?php

namespace Kilik\DbMonitorBundle\Entity\Traits;

trait DateTrait
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
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
