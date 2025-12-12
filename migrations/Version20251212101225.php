<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212101225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id BINARY(16) NOT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE thread_file (id BINARY(16) NOT NULL, thread_id BINARY(16) NOT NULL, file_id BINARY(16) NOT NULL, INDEX IDX_82E2A9EE2904019 (thread_id), INDEX IDX_82E2A9E93CB796C (file_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE thread_file ADD CONSTRAINT FK_82E2A9EE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE thread_file ADD CONSTRAINT FK_82E2A9E93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE thread_file DROP FOREIGN KEY FK_82E2A9EE2904019');
        $this->addSql('ALTER TABLE thread_file DROP FOREIGN KEY FK_82E2A9E93CB796C');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE thread_file');
    }
}
