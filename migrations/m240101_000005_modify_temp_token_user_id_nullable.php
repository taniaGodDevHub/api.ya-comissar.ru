<?php

use yii\db\Migration;

/**
 * Handles modifying the `user_id` column in table `{{%temp_token}}`.
 */
class m240101_000005_modify_temp_token_user_id_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Удаляем внешний ключ
        $this->dropForeignKey('fk-temp_token-user_id', '{{%temp_token}}');
        
        // Изменяем колонку user_id, чтобы она могла быть null
        $this->alterColumn('{{%temp_token}}', 'user_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Возвращаем колонку user_id как not null
        $this->alterColumn('{{%temp_token}}', 'user_id', $this->integer()->notNull());
        
        // Восстанавливаем внешний ключ
        $this->addForeignKey(
            'fk-temp_token-user_id',
            '{{%temp_token}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }
}






