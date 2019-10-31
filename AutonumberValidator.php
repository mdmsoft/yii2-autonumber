<?php

namespace mdm\autonumber;

use yii\db\ActiveRecord;
use yii\validators\Validator;

/**
 * Validator use to fill autonumber
 * 
 * Use to fill attribute with formatet autonumber.
 * 
 * Usage at [[$owner]] rules()
 * 
 * ~~~
 * return [
 *     [['sales_num'], 'autonumber', 'format'=>'SA.'.date('Ymd').'?'],
 *     ...
 * ]
 * ~~~
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AutonumberValidator extends Validator
{
    /**
     * @var mixed the default value or a PHP callable that returns the default value which will
     * be assigned to the attributes being validated if they are empty. The signature of the PHP callable
     * should be as follows,
     *
     * ```php
     * function foo($model, $attribute) {
     *     // compute value
     *     return $value;
     * }
     * ```
     * 
     * @see [[Behavior::$value]]
     */
    public $format;

    /**
     * @var integer digit number of auto number
     */
    public $digit;

    /**
     * @var mixed
     */
    public $group;

    /**
     * @var bool
     */
    public $alnum;

    /**
     * @var boolean
     */
    public $unique = true;

    /**
     * @inheritdoc
     */
    public $skipOnEmpty = false;

    /**
     * @var boolean
     */
    public $throwIsStale = false;

    /**
     * @var array
     */
    private static $_executed = [];

    /**
     * @inheritdoc
     */
    public function validateAttribute($object, $attribute)
    {
        if ($this->isEmpty($object->$attribute)) {
            $eventId = uniqid();
            $object->on(ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'beforeSave'], [$eventId, $attribute]);
            $object->on(ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'beforeSave'], [$eventId, $attribute]);
        }
    }

    /**
     * Handle for [[\yii\db\ActiveRecord::EVENT_BEFORE_INSERT]] and [[\yii\db\ActiveRecord::EVENT_BEFORE_UPDATE]]
     * @param \yii\base\ModelEvent $event
     */
    public function beforeSave($event)
    {
        list($id, $attribute) = $event->data;
        if (isset(self::$_executed[$id])) {
            return;
        }

        /* @var $object \yii\db\ActiveRecord */
        $object = $event->sender;
        if (is_string($this->format) && method_exists($object, $this->format)) {
            $value = call_user_func([$object, $this->format], $object, $attribute);
        } else {
            $value = is_callable($this->format) ? call_user_func($this->format, $object, $attribute) : $this->format;
        }

        $group = [
            'class' => $this->unique ? get_class($object) : false,
            'group' => $this->group,
            'attribute' => $attribute,
        ];
        $object->$attribute = AutoNumber::generate($value, $this->alnum, $this->digit, $group);
        self::$_executed[$id] = true;
    }
}
