<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109045816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commune (id INT AUTO_INCREMENT NOT NULL, wilaya_id INT NOT NULL, name VARCHAR(255) NOT NULL, arabic_name VARCHAR(255) NOT NULL, INDEX IDX_E2E2D1EEDC89F5B6 (wilaya_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, related_order_id INT NOT NULL, score INT NOT NULL, comment VARCHAR(1000) NOT NULL, UNIQUE INDEX UNIQ_794381C62B1C2395 (related_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wilaya (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, arabic_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commune ADD CONSTRAINT FK_E2E2D1EEDC89F5B6 FOREIGN KEY (wilaya_id) REFERENCES wilaya (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C62B1C2395 FOREIGN KEY (related_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` ADD commune_id INT NOT NULL, ADD validated_by_id INT DEFAULT NULL, ADD expediated_by_id INT DEFAULT NULL, ADD delivered_by_id INT DEFAULT NULL, ADD order_number VARCHAR(255) NOT NULL, ADD customer_name VARCHAR(255) NOT NULL, ADD customer_phone VARCHAR(25) NOT NULL, ADD customer_email VARCHAR(255) DEFAULT NULL, ADD customer_address VARCHAR(255) NOT NULL, ADD quantity_standard INT NOT NULL, ADD quantity_caramel INT NOT NULL, ADD total_price INT NOT NULL, ADD delivery_barcode VARCHAR(255) DEFAULT NULL, ADD status VARCHAR(50) NOT NULL, ADD save_date DATETIME NOT NULL, ADD validation_date DATETIME DEFAULT NULL, ADD expedition_date DATETIME DEFAULT NULL, ADD delivrance_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398131A4F72 FOREIGN KEY (commune_id) REFERENCES commune (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398C69DE5E5 FOREIGN KEY (validated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987A8D7EA8 FOREIGN KEY (expediated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398BFBEE0DA FOREIGN KEY (delivered_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F5299398131A4F72 ON `order` (commune_id)');
        $this->addSql('CREATE INDEX IDX_F5299398C69DE5E5 ON `order` (validated_by_id)');
        $this->addSql('CREATE INDEX IDX_F52993987A8D7EA8 ON `order` (expediated_by_id)');
        $this->addSql('CREATE INDEX IDX_F5299398BFBEE0DA ON `order` (delivered_by_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398131A4F72');
        $this->addSql('ALTER TABLE commune DROP FOREIGN KEY FK_E2E2D1EEDC89F5B6');
        $this->addSql('DROP TABLE commune');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE wilaya');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398C69DE5E5');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987A8D7EA8');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398BFBEE0DA');
        $this->addSql('DROP INDEX IDX_F5299398131A4F72 ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398C69DE5E5 ON `order`');
        $this->addSql('DROP INDEX IDX_F52993987A8D7EA8 ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398BFBEE0DA ON `order`');
        $this->addSql('ALTER TABLE `order` DROP commune_id, DROP validated_by_id, DROP expediated_by_id, DROP delivered_by_id, DROP order_number, DROP customer_name, DROP customer_phone, DROP customer_email, DROP customer_address, DROP quantity_standard, DROP quantity_caramel, DROP total_price, DROP delivery_barcode, DROP status, DROP save_date, DROP validation_date, DROP expedition_date, DROP delivrance_date');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
