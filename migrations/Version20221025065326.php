<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221025065326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD first_name VARCHAR(50) NOT NULL, ADD name VARCHAR(50) NOT NULL, ADD telephone VARCHAR(10) NOT NULL, ADD date_of_birth DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user DROP name, DROP first_name, DROP date_of_birth, DROP telephone');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP first_name, DROP name, DROP telephone, DROP date_of_birth');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(100) NOT NULL, ADD first_name VARCHAR(100) NOT NULL, ADD date_of_birth DATETIME NOT NULL, ADD telephone VARCHAR(20) DEFAULT NULL');
    }
}
