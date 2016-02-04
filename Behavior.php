<?php

namespace mdm\autonumber;

use yii\db\StaleObjectException;
use yii\db\BaseActiveRecord;
use Exception;

/**
 * Behavior use to generate formated autonumber.
 * Use at ActiveRecord behavior
 * 
 * ~~~
 * public function behavior()
 * {
 *     return [
 *         ...
 *         [
 *             'class' => 'mdm\autonumber\Behavior',
 *             'value' => date('Ymd').'.?', // ? will replace with generated number
 *             'digit' => 6, // specify this if you need leading zero for number
 *         ]
 *     ]
 * }
 * ~~~
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Behavior extends \yii\behaviors\AttributeBehavior
{
    /**
     * @var integer digit number of auto number
     */
    public $digit;

    /**
     * @var mixed Optional. 
     */
    public $group;

    /**
     * @var boolean If set `true` number will genarate unique for owner classname.
     * Default `true`. 
     */
    public $unique = true;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->attribute !== null) {
            $this->attributes[BaseActiveRecord::EVENT_BEFORE_INSERT][] = $this->attribute;
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        $value = parent::getValue($event);
        $group = md5(serialize([
            'class' => $this->unique ? get_class($this->owner) : false,
            'group' => $this->group,
            'attribute' => $this->attribute,
            'value' => $value
        ]));
        do {
            $repeat = false;
            try {
                $model = AutoNumber::findOne($group);
                if ($model) {
                    $number = $model->number + 1;
                } else {
                    $model = new AutoNumber([
                        'group' => $group
                    ]);
                    $number = 1;
                }
                $model->update_time = time();
                $model->number = $number;
                $model->save(false);
            } catch (Exception $exc) {
                if ($exc instanceof StaleObjectException) {
                    $repeat = true;
                } else {
                    throw $exc;
                }
            }
        } while ($repeat);
        if ($value === null) {
            return $number;
        } else {
            return str_replace('?', $this->digit ? sprintf("%0{$this->digit}d", $number) : $number, $value);
        }
    }
}
