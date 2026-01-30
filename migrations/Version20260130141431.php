<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260130141431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add tax-product relation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD tax_id INT NOT NULL');
        $this->addSql('UPDATE product SET tax_id = 2');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB2A824D8 FOREIGN KEY (tax_id) REFERENCES sales_tax (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADB2A824D8 ON product (tax_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB2A824D8');
        $this->addSql('DROP INDEX IDX_D34A04ADB2A824D8 ON product');
        $this->addSql('ALTER TABLE product DROP tax_id');
    }
}
