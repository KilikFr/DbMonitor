<?php

namespace Kilik\DbMonitorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * History.
 *
 * @ORM\MappedSuperclass()
 */
abstract class AbstractHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="db_name", type="string", length=255)
     */
    private $dbName;

    /**
     * @var string
     *
     * @ORM\Column(name="table_name", type="string", length=255)
     */
    private $tableName;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_rows", type="integer")
     */
    private $nbRows;

    /**
     * @var int
     *
     * @ORM\Column(name="data_length", type="bigint")
     */
    private $dataLength;

    /**
     * @var int
     *
     * @ORM\Column(name="index_length", type="bigint")
     */
    private $indexLength;

    /**
     * Server.
     *
     * @var Server
     *
     * @ORM\ManyToOne(targetEntity="\Kilik\DbMonitorBundle\Entity\Server")
     * @ORM\JoinColumn(name="id_server", referencedColumnName="id", nullable=false)
     */
    private $server;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dbName.
     *
     * @param string $dbName
     *
     * @return static
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;

        return $this;
    }

    /**
     * Get dbName.
     *
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Set tableName.
     *
     * @param string $tableName
     *
     * @return static
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get tableName.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set nbRows.
     *
     * @param int $nbRows
     *
     * @return static
     */
    public function setNbRows($nbRows)
    {
        $this->nbRows = $nbRows;

        return $this;
    }

    /**
     * Get nbRows.
     *
     * @return int
     */
    public function getNbRows()
    {
        return $this->nbRows;
    }

    /**
     * Set dataLength.
     *
     * @param int $dataLength
     *
     * @return static
     */
    public function setDataLength($dataLength)
    {
        $this->dataLength = $dataLength;

        return $this;
    }

    /**
     * Get dataLength.
     *
     * @return int
     */
    public function getDataLength()
    {
        return $this->dataLength;
    }

    /**
     * Set indexLength.
     *
     * @param int $indexLength
     *
     * @return static
     */
    public function setIndexLength($indexLength)
    {
        $this->indexLength = $indexLength;

        return $this;
    }

    /**
     * Get indexLength.
     *
     * @return int
     */
    public function getIndexLength()
    {
        return $this->indexLength;
    }

    /**
     * @param Server $server
     *
     * @return static
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }
}
