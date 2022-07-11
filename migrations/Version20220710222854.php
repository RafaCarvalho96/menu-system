<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220710222854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_7D053A93B092A811');
        $this->addSql('CREATE TEMPORARY TABLE __temp__menu AS SELECT id, store_id, name, file FROM menu');
        $this->addSql('DROP TABLE menu');
        $this->addSql('CREATE TABLE menu (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, store_id INTEGER NOT NULL, name VARCHAR(255) DEFAULT NULL, file VARCHAR(255) NOT NULL, CONSTRAINT FK_7D053A93B092A811 FOREIGN KEY (store_id) REFERENCES store (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO menu (id, store_id, name, file) SELECT id, store_id, name, file FROM __temp__menu');
        $this->addSql('DROP TABLE __temp__menu');
        $this->addSql('CREATE INDEX IDX_7D053A93B092A811 ON menu (store_id)');
        $this->addSql('DROP INDEX IDX_FF5758777E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__store AS SELECT id, owner_id, name, description, phone, address FROM store');
        $this->addSql('DROP TABLE store');
        $this->addSql('CREATE TABLE store (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, phone CLOB NOT NULL --(DC2Type:array)
        , address CLOB DEFAULT NULL --(DC2Type:array)
        , CONSTRAINT FK_FF5758777E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO store (id, owner_id, name, description, phone, address) SELECT id, owner_id, name, description, phone, address FROM __temp__store');
        $this->addSql('DROP TABLE __temp__store');
        $this->addSql('CREATE INDEX IDX_FF5758777E3C61F9 ON store (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_7D053A93B092A811');
        $this->addSql('CREATE TEMPORARY TABLE __temp__menu AS SELECT id, store_id, name, file FROM menu');
        $this->addSql('DROP TABLE menu');
        $this->addSql('CREATE TABLE menu (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, store_id INTEGER NOT NULL, name VARCHAR(255) DEFAULT NULL, file VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO menu (id, store_id, name, file) SELECT id, store_id, name, file FROM __temp__menu');
        $this->addSql('DROP TABLE __temp__menu');
        $this->addSql('CREATE INDEX IDX_7D053A93B092A811 ON menu (store_id)');
        $this->addSql('DROP INDEX IDX_FF5758777E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__store AS SELECT id, owner_id, name, description, phone, address FROM store');
        $this->addSql('DROP TABLE store');
        $this->addSql('CREATE TABLE store (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, phone CLOB NOT NULL --(DC2Type:array)
        , address CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO store (id, owner_id, name, description, phone, address) SELECT id, owner_id, name, description, phone, address FROM __temp__store');
        $this->addSql('DROP TABLE __temp__store');
        $this->addSql('CREATE INDEX IDX_FF5758777E3C61F9 ON store (owner_id)');
    }
}
