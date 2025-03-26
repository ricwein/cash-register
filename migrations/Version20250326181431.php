<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326181431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add button_sound setting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<QUERY
                INSERT INTO `setting` (`name`, `description`, `is_on`)
                VALUES (
                    'button_sound',
                    'Akustisches Feedback (Ton) für Kassen-Schaltflächen.',
                    1
                );
            QUERY
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `setting` WHERE name = 'button_sound'");
    }
}
