<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220804094110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE input (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE input_benefit (input_id INT NOT NULL, benefit_id INT NOT NULL, INDEX IDX_499D99DF36421AD6 (input_id), INDEX IDX_499D99DFB517B89 (benefit_id), PRIMARY KEY(input_id, benefit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE input_benefit ADD CONSTRAINT FK_499D99DF36421AD6 FOREIGN KEY (input_id) REFERENCES input (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE input_benefit ADD CONSTRAINT FK_499D99DFB517B89 FOREIGN KEY (benefit_id) REFERENCES benefit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE input_benefit DROP FOREIGN KEY FK_499D99DF36421AD6');
        $this->addSql('DROP TABLE input');
        $this->addSql('DROP TABLE input_benefit');
    }
}
