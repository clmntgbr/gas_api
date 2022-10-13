<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221013195857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station CHANGE last_gas_prices last_gas_prices LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE textsearch_api_result textsearch_api_result LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE place_details_api_result place_details_api_result LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station CHANGE last_gas_prices last_gas_prices LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE textsearch_api_result textsearch_api_result LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE place_details_api_result place_details_api_result LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
