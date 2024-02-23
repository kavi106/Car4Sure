<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240223073336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, state VARCHAR(15) NOT NULL, zip INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE coverage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, vehicle_id INTEGER NOT NULL, type VARCHAR(20) NOT NULL, coverage_limit INTEGER NOT NULL, deductible INTEGER NOT NULL, CONSTRAINT FK_5556F36B545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5556F36B545317D1 ON coverage (vehicle_id)');
        $this->addSql('CREATE TABLE person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, address_id INTEGER DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, date_of_birth DATE DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, marital_status VARCHAR(10) DEFAULT NULL, license_number INTEGER DEFAULT NULL, license_state VARCHAR(15) DEFAULT NULL, license_status VARCHAR(10) DEFAULT NULL, license_effective_date DATE DEFAULT NULL, license_expiration_date DATE DEFAULT NULL, license_class VARCHAR(1) DEFAULT NULL, CONSTRAINT FK_34DCD176F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176F5B7AF75 ON person (address_id)');
        $this->addSql('CREATE TABLE policy (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, policy_holder_id INTEGER NOT NULL, user_id INTEGER NOT NULL, policy_status VARCHAR(10) NOT NULL, policy_type VARCHAR(10) NOT NULL, policy_effective_date DATE NOT NULL, policy_expiration_date DATE NOT NULL, CONSTRAINT FK_F07D0516A07EC9B5 FOREIGN KEY (policy_holder_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F07D0516A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F07D0516A07EC9B5 ON policy (policy_holder_id)');
        $this->addSql('CREATE INDEX IDX_F07D0516A76ED395 ON policy (user_id)');
        $this->addSql('CREATE TABLE policy_vehicle (policy_id INTEGER NOT NULL, vehicle_id INTEGER NOT NULL, PRIMARY KEY(policy_id, vehicle_id), CONSTRAINT FK_78AC5CB22D29E3C6 FOREIGN KEY (policy_id) REFERENCES policy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_78AC5CB2545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_78AC5CB22D29E3C6 ON policy_vehicle (policy_id)');
        $this->addSql('CREATE INDEX IDX_78AC5CB2545317D1 ON policy_vehicle (vehicle_id)');
        $this->addSql('CREATE TABLE policy_person (policy_id INTEGER NOT NULL, person_id INTEGER NOT NULL, PRIMARY KEY(policy_id, person_id), CONSTRAINT FK_1E083C6B2D29E3C6 FOREIGN KEY (policy_id) REFERENCES policy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1E083C6B217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1E083C6B2D29E3C6 ON policy_person (policy_id)');
        $this->addSql('CREATE INDEX IDX_1E083C6B217BBB47 ON policy_person (person_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE TABLE vehicle (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, garaging_address_id INTEGER DEFAULT NULL, year INTEGER NOT NULL, make VARCHAR(50) NOT NULL, model VARCHAR(50) NOT NULL, vin BIGINT NOT NULL, usage VARCHAR(100) NOT NULL, primary_use VARCHAR(100) NOT NULL, annual_mileage INTEGER NOT NULL, ownership VARCHAR(100) NOT NULL, CONSTRAINT FK_1B80E4867A926313 FOREIGN KEY (garaging_address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1B80E486B1085141 ON vehicle (vin)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1B80E4867A926313 ON vehicle (garaging_address_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE coverage');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE policy');
        $this->addSql('DROP TABLE policy_vehicle');
        $this->addSql('DROP TABLE policy_person');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
