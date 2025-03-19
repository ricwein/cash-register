<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319123014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, priority INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, color CHAR(7) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15E237E06 ON category (name)');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, priority INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, products_per_row INTEGER NOT NULL, use_category_tabs BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, priority INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, color CHAR(7) DEFAULT NULL, icon VARCHAR(255) NOT NULL, category_id INTEGER DEFAULT NULL, event_id INTEGER NOT NULL, CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D34A04AD71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD71F7E88B ON product (event_id)');
        $this->addSql('CREATE TABLE purchase_transaction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, transaction_id BLOB NOT NULL, payment_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, price NUMERIC(10, 2) NOT NULL, event_name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98A1D282FC0CB0F ON purchase_transaction (transaction_id)');
        $this->addSql('CREATE TABLE purchased_article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, product_name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, quantity INTEGER NOT NULL, receipt_transaction_id INTEGER NOT NULL, CONSTRAINT FK_F3D4A2462BFB24F FOREIGN KEY (receipt_transaction_id) REFERENCES purchase_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F3D4A2462BFB24F ON purchased_article (receipt_transaction_id)');
        $this->addSql('CREATE TABLE setting (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_on BOOLEAN NOT NULL, description CLOB DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F74B8985E237E06 ON setting (name)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE purchase_transaction');
        $this->addSql('DROP TABLE purchased_article');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
