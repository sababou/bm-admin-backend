<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109085204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987A8D7EA8');
        $this->addSql('DROP INDEX IDX_F52993987A8D7EA8 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD returned_by_id INT DEFAULT NULL, ADD entrusted_to_id INT DEFAULT NULL, ADD shipment_date DATETIME DEFAULT NULL, ADD delivery_date DATETIME DEFAULT NULL, ADD return_date DATETIME DEFAULT NULL, DROP expedition_date, DROP delivrance_date, CHANGE expediated_by_id shipped_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987D8809DC FOREIGN KEY (shipped_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939871AD87D9 FOREIGN KEY (returned_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993988069B8DF FOREIGN KEY (entrusted_to_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F52993987D8809DC ON `order` (shipped_by_id)');
        $this->addSql('CREATE INDEX IDX_F529939871AD87D9 ON `order` (returned_by_id)');
        $this->addSql('CREATE INDEX IDX_F52993988069B8DF ON `order` (entrusted_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987D8809DC');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939871AD87D9');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993988069B8DF');
        $this->addSql('DROP INDEX IDX_F52993987D8809DC ON `order`');
        $this->addSql('DROP INDEX IDX_F529939871AD87D9 ON `order`');
        $this->addSql('DROP INDEX IDX_F52993988069B8DF ON `order`');
        $this->addSql('ALTER TABLE `order` ADD expediated_by_id INT DEFAULT NULL, ADD expedition_date DATETIME DEFAULT NULL, ADD delivrance_date DATETIME DEFAULT NULL, DROP shipped_by_id, DROP returned_by_id, DROP entrusted_to_id, DROP shipment_date, DROP delivery_date, DROP return_date');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987A8D7EA8 FOREIGN KEY (expediated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F52993987A8D7EA8 ON `order` (expediated_by_id)');
    }
}
