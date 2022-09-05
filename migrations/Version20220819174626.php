<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220819174626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reply (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, content LONGTEXT NOT NULL, INDEX IDX_FDA8C6E0F624B39D (sender_id), INDEX IDX_FDA8C6E0E92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0E92F8F78 FOREIGN KEY (recipient_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D727ACA70');
        $this->addSql('DROP INDEX IDX_5A8A6C8D727ACA70 ON post');
        $this->addSql('ALTER TABLE post DROP parent_id, DROP rgpd');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reply');
        $this->addSql('ALTER TABLE post ADD parent_id INT DEFAULT NULL, ADD rgpd TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D727ACA70 FOREIGN KEY (parent_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D727ACA70 ON post (parent_id)');
    }
}
