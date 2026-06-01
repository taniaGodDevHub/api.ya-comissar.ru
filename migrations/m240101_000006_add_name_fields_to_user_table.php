<?php

use yii\db\Migration;

/**
 * Добавляет поля first_name и last_name в таблицу user
 */
class m240101_000006_add_name_fields_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'first_name', $this->string(100)->notNull()->after('username'));
        $this->addColumn('{{%user}}', 'last_name', $this->string(100)->notNull()->after('first_name'));
        
        // Добавляем индексы для поиска по имени и фамилии
        $this->createIndex('idx-user-first_name', '{{%user}}', 'first_name');
        $this->createIndex('idx-user-last_name', '{{%user}}', 'last_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user-last_name', '{{%user}}');
        $this->dropIndex('idx-user-first_name', '{{%user}}');
        $this->dropColumn('{{%user}}', 'last_name');
        $this->dropColumn('{{%user}}', 'first_name');
    }
}
