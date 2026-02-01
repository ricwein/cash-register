<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260130124710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sales-tax table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sales_tax (id INT AUTO_INCREMENT NOT NULL, percent INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('INSERT INTO sales_tax (id, percent) VALUES (1, 19), (2, 7)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sales_tax');
    }
}
