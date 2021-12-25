<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211217114419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE login_token (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, generated_date DATETIME NOT NULL, limit_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE login_trace (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, login_token_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_372B1235A76ED395 (user_id), INDEX IDX_372B12356F7AC726 (login_token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE login_trace ADD CONSTRAINT FK_372B1235A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE login_trace ADD CONSTRAINT FK_372B12356F7AC726 FOREIGN KEY (login_token_id) REFERENCES login_token (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login_trace DROP FOREIGN KEY FK_372B12356F7AC726');
        $this->addSql('DROP TABLE login_token');
        $this->addSql('DROP TABLE login_trace');
    }
}
