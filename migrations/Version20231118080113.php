<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231118080113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE child ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B3542912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_22B3542912469DE2 ON child (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B3542912469DE2');
        $this->addSql('DROP INDEX IDX_22B3542912469DE2 ON child');
        $this->addSql('ALTER TABLE child DROP category_id');
    }
}
