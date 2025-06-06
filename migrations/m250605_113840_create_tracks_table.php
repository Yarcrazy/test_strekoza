<?php

use app\enums\TrackStatus;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%tracks}}`.
 */
class m250605_113840_create_tracks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->execute("CREATE TYPE track_status AS ENUM ('" . implode("','", array_map(fn ($case) => $case->value, TrackStatus::cases())) . "')");

        $this->createTable('{{%tracks}}', [
            'id' => $this->bigPrimaryKey(),
            'track_number' => $this->string()->notNull()->unique(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' => "track_status NOT NULL DEFAULT '" . TrackStatus::NEW->value . "'",
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%tracks}}');
        $this->execute('DROP TYPE track_status');
    }
}
