<?php

namespace mdm\autonumber;

/**
 * This is the model class for table "auto_number".
 *
 * @property string $group
 * @property string $template
 * @property integer $number
 */
class AutoNumber extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auto_number}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['optimistic_lock', 'number'], 'default', 'value' => 1],
            [['group'], 'required'],
            [['number'], 'integer'],
            [['group'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'template' => 'Template Num',
            'number' => 'Number',
        ];
    }

    public function optimisticLock()
    {
        return 'optimistic_lock';
    }
}