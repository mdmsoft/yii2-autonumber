<?php

namespace mdm\autonumber;

use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Validator use to fill autonumber
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class NextValueValidator extends \yii\validators\Validator
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
            $object->$attribute = $this->nextValue($object, $attribute);
        }
    }

    /**
     * Calculate next value
     * @param  \yii\db\ActiveRecord $object
     * @param  string               $attribute
     * @return int
     */
    public function nextValue($object, $attribute)
    {
        if ($this->format instanceof \Closure) {
            $value = call_user_func($this->format, $object, $attribute);
        } else {
            $value = $this->format;
        }

        $group = md5(serialize([
            'class' => $this->unique ? get_class($object) : false,
            'group' => $this->group,
            'attribute' => $this->attribute,
            'value' => $value
        ]));
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

        $eventId = uniqid();
        $object->on(ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'beforeSave'], [$model, $eventId]);
        $object->on(ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'beforeSave'], [$model, $eventId]);

        if ($value === null) {
            return $number;
        } else {
            return str_replace('?', $this->digit ? sprintf("%0{$this->digit}d", $number) : $number, $value);
        }
    }

    /**
     * Handle for [[\yii\base\ModelEvent::EVENT_BEFORE_SAVE]]
     * @param \yii\base\ModelEvent $event
     */
    public function beforeSave($event)
    {
        /* @var $model AutoNumber */
        list($model, $id) = $event->data;
        if (isset(static::$_executed[$id])) {
            return;
        }
        static::$_executed[$id] = true;
        try {
            $model->save();
        } catch (\Exception $exc) {
            $event->isValid = false;
            if ($this->throwIsStale || !($exc instanceof StaleObjectException)) {
                throw $exc;
            }
        }
    }
}
