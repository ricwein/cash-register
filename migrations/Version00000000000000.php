<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version00000000000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, priority INT DEFAULT NULL, name VARCHAR(255) NOT NULL, color CHAR(7) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, priority INT DEFAULT NULL, name VARCHAR(255) NOT NULL, products_per_row INT NOT NULL, use_category_tabs TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, priority INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, color CHAR(7) DEFAULT NULL, icon VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_event (product_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_9AF271FB4584665A (product_id), INDEX IDX_9AF271FB71F7E88B (event_id), PRIMARY KEY(product_id, event_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE purchase_transaction (id INT AUTO_INCREMENT NOT NULL, transaction_id BINARY(16) NOT NULL, payment_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, price NUMERIC(10, 2) NOT NULL, event_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_98A1D282FC0CB0F (transaction_id), INDEX IDX_98A1D288B8E842841E832AD (created_at, event_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE purchased_article (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, receipt_transaction_id INT NOT NULL, INDEX IDX_F3D4A2462BFB24F (receipt_transaction_id), INDEX IDX_F3D4A2464584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_on TINYINT(1) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_9F74B8985E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_event ADD CONSTRAINT FK_9AF271FB4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_event ADD CONSTRAINT FK_9AF271FB71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchased_article ADD CONSTRAINT FK_F3D4A2462BFB24F FOREIGN KEY (receipt_transaction_id) REFERENCES purchase_transaction (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product_event DROP FOREIGN KEY FK_9AF271FB4584665A');
        $this->addSql('ALTER TABLE product_event DROP FOREIGN KEY FK_9AF271FB71F7E88B');
        $this->addSql('ALTER TABLE purchased_article DROP FOREIGN KEY FK_F3D4A2462BFB24F');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_event');
        $this->addSql('DROP TABLE purchase_transaction');
        $this->addSql('DROP TABLE purchased_article');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
