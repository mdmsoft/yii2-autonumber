<?php

namespace mdm\autonumber;

/**
 * This is the model class for table "auto_number".
 *
 * @property string $template_num
 * @property integer $auto_number
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
            [['template_group', 'template_num', 'auto_number'], 'required'],
            [['optimistic_lock'], 'default', 'value' => 1],
            [['auto_number'], 'integer'],
            [['template_group', 'template_num'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'template_num' => 'Template Num',
            'auto_number' => 'Auto Number',
        ];
    }

    public function optimisticLock()
    {
        return 'optimistic_lock';
    }
}