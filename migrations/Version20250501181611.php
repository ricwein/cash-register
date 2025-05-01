<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501181611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add setting icon';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting ADD icon VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE setting SET icon = "fa-solid fa-table-columns" WHERE name = "landscape_mode"');
        $this->addSql('UPDATE setting SET icon = "fa-solid fa-bell" WHERE name = "button_sound"');
        $this->addSql('UPDATE setting SET icon = "fa-solid fa-gauge-high" WHERE name = "quick_checkout"');
        $this->addSql('UPDATE setting SET icon = "fa-solid fa-calculator" WHERE name = "lazy_calculator"');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE setting DROP icon
        SQL);
    }
}
