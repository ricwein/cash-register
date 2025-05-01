<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501145636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add quick_checkout and lazy_calculator setting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<QUERY
                INSERT INTO `setting` (`name`, `description`, `is_on`)
                VALUES (
                    'quick_checkout',
                    '"quittieren" Schaltfläche direkt durch Karte/Bar-Auswahl ersetzen.',
                    1
                ) ON DUPLICATE KEY UPDATE description = VALUES(description);
            QUERY
        );
        $this->addSql(
            <<<QUERY
                INSERT INTO `setting` (`name`, `description`, `is_on`)
                VALUES (
                    'lazy_calculator',
                    'Rückgeld-Rechner erst bei Klick öffnen',
                    1
                ) ON DUPLICATE KEY UPDATE description = VALUES(description);
            QUERY
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `setting` WHERE name = 'quick_checkout'");
    }
}
