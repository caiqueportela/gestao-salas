<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190831203643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE agendamentos (id INT AUTO_INCREMENT NOT NULL, sala_id INT NOT NULL, inicio DATETIME NOT NULL, fim DATETIME NOT NULL, observacao VARCHAR(255) DEFAULT NULL, INDEX IDX_2D12EA4AC51CDF3F (sala_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salas (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, descricao VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agendamentos ADD CONSTRAINT FK_2D12EA4AC51CDF3F FOREIGN KEY (sala_id) REFERENCES salas (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agendamentos DROP FOREIGN KEY FK_2D12EA4AC51CDF3F');
        $this->addSql('DROP TABLE agendamentos');
        $this->addSql('DROP TABLE salas');
    }
}
