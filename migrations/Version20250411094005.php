<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411094005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c84955b83297e7');
        $this->addSql('DROP INDEX idx_42c84955b83297e7');
        $this->addSql('ALTER TABLE reservation DROP reservation_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reservation ADD reservation_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c84955b83297e7 FOREIGN KEY (reservation_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_42c84955b83297e7 ON reservation (reservation_id)');
    }
}
