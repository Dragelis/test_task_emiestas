<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;

use Doctrine\DBAL\Schema\Schema;

final class Version20190813145238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fight bets initialization';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fight_bet (id INT AUTO_INCREMENT NOT NULL, fight_id INT DEFAULT NULL, user_id INT DEFAULT NULL, `option` INT NOT NULL, INDEX IDX_37EFC43EAC6657E4 (fight_id), INDEX IDX_37EFC43EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fight_bet ADD CONSTRAINT FK_37EFC43EAC6657E4 FOREIGN KEY (fight_id) REFERENCES fight (id)');
        $this->addSql('ALTER TABLE fight_bet ADD CONSTRAINT FK_37EFC43EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE fight_bet');
    }
}
