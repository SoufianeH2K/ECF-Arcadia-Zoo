<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240519181132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE nouriture (id INT AUTO_INCREMENT NOT NULL, animal_id INT NOT NULL, type VARCHAR(100) NOT NULL, quantite INT NOT NULL, date DATE NOT NULL, INDEX IDX_814DA9268E962C16 (animal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nouriture ADD CONSTRAINT FK_814DA9268E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nouriture DROP FOREIGN KEY FK_814DA9268E962C16');
        $this->addSql('DROP TABLE nouriture');

    }
}
