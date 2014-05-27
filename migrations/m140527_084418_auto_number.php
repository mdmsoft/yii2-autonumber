<?php

use yii\db\Schema;

class m140527_084418_auto_number extends \yii\db\Migration
{

    public function safeUp()
    {
        $this->createTable('{{%auto_number}}', [
            'template_group' => Schema::TYPE_STRING . '(64) NOT NULL',
            'template_num' => Schema::TYPE_STRING . '(64) NOT NULL', 
            'auto_number' => Schema::TYPE_INTEGER . ' NOT NULL',
            'optimistic_lock' => Schema::TYPE_INTEGER . ' NOT NULL',
            'update_time' => Schema::TYPE_INTEGER,
        ]);
        $this->addPrimaryKey('auto_number_pk', '{{%auto_number}}', ['template_group','template_num']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%auto_number}}');
    }
}