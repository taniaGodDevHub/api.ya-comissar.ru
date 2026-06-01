<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%temp_token}}`.
 */
class m240101_000002_create_temp_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%temp_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(32)->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-temp_token-user_id', '{{%temp_token}}', 'user_id');
        $this->createIndex('idx-temp_token-token', '{{%temp_token}}', 'token');

        $this->addForeignKey(
            'fk-temp_token-user_id',
            '{{%temp_token}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-temp_token-user_id', '{{%temp_token}}');
        $this->dropTable('{{%temp_token}}');
    }
}






