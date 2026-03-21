<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314184102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add article_quantity_selection setting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<QUERY
                INSERT INTO `setting` (`name`, `is_on`, `description`, `icon`)
                VALUES (
                    'article_quantity_selection',
                    1,
                    'Eingabe von Artikel-Anzahl anzeigen',
                    'fa-solid fa-keyboard'
                ) ON DUPLICATE KEY UPDATE description = VALUES(description);
            QUERY
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `setting` WHERE name = 'article_quantity_selection'");
    }
}
