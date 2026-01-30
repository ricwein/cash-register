<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260130143322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Store product tax in purchased_article';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE purchased_article ADD tax INT NOT NULL');
        $this->addSql('UPDATE purchased_article SET tax = 19');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE purchased_article DROP tax');
    }
}
