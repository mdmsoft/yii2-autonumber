<?php

use yii\db\Schema;

class m140527_084418_auto_number extends \yii\db\Migration
{

    public function safeUp()
    {
        $this->createTable('{{%auto_number}}', [
            'group' => Schema::TYPE_STRING . '(32) NOT NULL',
            'template' => Schema::TYPE_STRING . '(64) NOT NULL', 
            'number' => Schema::TYPE_INTEGER . ' NOT NULL',
            'optimistic_lock' => Schema::TYPE_INTEGER . ' NOT NULL',
            'update_time' => Schema::TYPE_INTEGER,
            'PRIMARY KEY ([[group]], [[template]])'
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%auto_number}}');
    }
}