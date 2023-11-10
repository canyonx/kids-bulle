<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231110103159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_child (activity_id INT NOT NULL, child_id INT NOT NULL, INDEX IDX_3CB1629A81C06096 (activity_id), INDEX IDX_3CB1629ADD62C21B (child_id), PRIMARY KEY(activity_id, child_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_child ADD CONSTRAINT FK_3CB1629A81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_child ADD CONSTRAINT FK_3CB1629ADD62C21B FOREIGN KEY (child_id) REFERENCES child (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity ADD teacher_id INT DEFAULT NULL, ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_AC74095A41807E1D ON activity (teacher_id)');
        $this->addSql('CREATE INDEX IDX_AC74095A12469DE2 ON activity (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_child DROP FOREIGN KEY FK_3CB1629A81C06096');
        $this->addSql('ALTER TABLE activity_child DROP FOREIGN KEY FK_3CB1629ADD62C21B');
        $this->addSql('DROP TABLE activity_child');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A41807E1D');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A12469DE2');
        $this->addSql('DROP INDEX IDX_AC74095A41807E1D ON activity');
        $this->addSql('DROP INDEX IDX_AC74095A12469DE2 ON activity');
        $this->addSql('ALTER TABLE activity DROP teacher_id, DROP category_id');
    }
}
