<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170623144245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE daily_history (id INT AUTO_INCREMENT NOT NULL, id_server INT NOT NULL, db_name VARCHAR(255) NOT NULL, table_name VARCHAR(255) NOT NULL, nb_rows INT NOT NULL, data_length BIGINT NOT NULL, index_length BIGINT NOT NULL, date DATE NOT NULL, INDEX IDX_F85BA1BF7C5A601B (id_server), UNIQUE INDEX uniq_idx (id_server, db_name, table_name, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hourly_history (id INT AUTO_INCREMENT NOT NULL, id_server INT NOT NULL, db_name VARCHAR(255) NOT NULL, table_name VARCHAR(255) NOT NULL, nb_rows INT NOT NULL, data_length BIGINT NOT NULL, index_length BIGINT NOT NULL, date DATETIME NOT NULL, INDEX IDX_FC10254A7C5A601B (id_server), UNIQUE INDEX uniq_idx (id_server, db_name, table_name, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monthly_history (id INT AUTO_INCREMENT NOT NULL, id_server INT NOT NULL, db_name VARCHAR(255) NOT NULL, table_name VARCHAR(255) NOT NULL, nb_rows INT NOT NULL, data_length BIGINT NOT NULL, index_length BIGINT NOT NULL, date DATE NOT NULL, INDEX IDX_2BEFCBE07C5A601B (id_server), UNIQUE INDEX uniq_idx (id_server, db_name, table_name, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, host VARCHAR(255) NOT NULL, port INT NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, disabled TINYINT(1) NOT NULL, UNIQUE INDEX name_idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE daily_history ADD CONSTRAINT FK_F85BA1BF7C5A601B FOREIGN KEY (id_server) REFERENCES server (id)');
        $this->addSql('ALTER TABLE hourly_history ADD CONSTRAINT FK_FC10254A7C5A601B FOREIGN KEY (id_server) REFERENCES server (id)');
        $this->addSql('ALTER TABLE monthly_history ADD CONSTRAINT FK_2BEFCBE07C5A601B FOREIGN KEY (id_server) REFERENCES server (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE daily_history DROP FOREIGN KEY FK_F85BA1BF7C5A601B');
        $this->addSql('ALTER TABLE hourly_history DROP FOREIGN KEY FK_FC10254A7C5A601B');
        $this->addSql('ALTER TABLE monthly_history DROP FOREIGN KEY FK_2BEFCBE07C5A601B');
        $this->addSql('DROP TABLE daily_history');
        $this->addSql('DROP TABLE hourly_history');
        $this->addSql('DROP TABLE monthly_history');
        $this->addSql('DROP TABLE server');
    }
}
