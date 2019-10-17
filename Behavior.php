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
     *
     * @var bool If set `true` formated number will return alfabet and numeric.
     */
    public $alnum = false;

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
        if (is_string($this->value) && method_exists($this->owner, $this->value)) {
            $value = call_user_func([$this->owner, $this->value], $event);
        } else {
            $value = is_callable($this->value) ? call_user_func($this->value, $event) : $this->value;
        }
        $group = [
            'class' => $this->unique ? get_class($this->owner) : false,
            'group' => $this->group,
            'attribute' => $this->attribute,
        ];
        return AutoNumber::generate($value, $this->alnum, $this->digit, $group);
    }
}
