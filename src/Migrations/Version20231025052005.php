<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231025052005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create votes table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->createTable('votes');

        // Add columns to the table
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('username', 'string', ['length' => 255]);
        $table->addColumn('score', 'integer', ['length' =>5]);
        // Add more columns as needed

        // Define primary key
        $table->setPrimaryKey(['id']);

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('votes');

    }
}
