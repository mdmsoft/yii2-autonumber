<?php

namespace mdm\autonumber;

/**
 * Description of NextValueValidator
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class NextValueValidator extends \yii\validators\Validator
{
    /**
     * @var string the name of the ActiveRecord class that should be used to validate the existence
     * of the current attribute value. It not set, it will use the ActiveRecord class of the attribute being validated.
     * @see targetAttribute
     */
    public $targetClass;

    /**
     * @var string|array the name of the ActiveRecord attribute that should be used to
     * validate the existence of the current attribute value. If not set, it will use the name
     * of the attribute currently being validated. You may use an array to validate the existence
     * of multiple columns at the same time. The array values are the attributes that will be
     * used to validate the existence, while the array keys are the attributes whose values are to be validated.
     * If the key and the value are the same, you can just specify the value.
     */
    public $targetAttribute;

    /**
     * @var string|array|\Closure additional filter to be applied to the DB query used to check the existence of the attribute value.
     * This can be a string or an array representing the additional query condition (refer to [[\yii\db\Query::where()]]
     * on the format of query condition), or an anonymous function with the signature `function ($query)`, where `$query`
     * is the [[\yii\db\Query|Query]] object that you can modify in the function.
     */
    public $filter;

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
    public $template;

    /**
     *
     * @var integer digit number of auto number
     */
    public $digit;

    /**
     * @var boolean this property is overwritten to be false so that this validator will
     * be applied when the value being validated is empty.
     */
    public $skipOnEmpty = false;

    /**
     * @inheritdoc
     */
    public function validateAttribute($object, $attribute)
    {
        if ($this->isEmpty($object->$attribute)) {
            $object->$attribute = $this->nextValue($object, $attribute);
        }
    }

    public function nextValue($object, $attribute)
    {
        if ($this->template instanceof \Closure) {
            $template = call_user_func($this->template, $object, $attribute);
        } else {
            $template = $this->template;
        }

        $targetAttribute = $this->targetAttribute === null ? $attribute : $this->targetAttribute;
        $targetClass = $this->targetClass === null ? get_class($object) : $this->targetClass;

        if ($template !== null) {
            $value = strtr($template, ['%' => '\%', '_' => '\_', '\\' => '\\\\', '?' => '%']);
            $params = ['like', $targetAttribute, $value, false];
        } else {
            $params = [];
        }

        $query = $this->createQuery($targetClass, $params);

        $current = $query->max($targetAttribute);
        if ($current) {
            if ($template === null) {
                $number = $current + 1;
            } else {
                $matches = [];
                $pattren = '/' . strtr($template, ['.' => '\.', '+' => '\+', '\\' => '\\\\', '?' => '(\d+)']) . '/';
                preg_match($pattren, $current, $matches);
                $number = empty($matches[1]) ? 1 : $matches[1] + 1;
            }
        } else {
            $number = 1;
        }

        if ($template === null) {
            return $this->digit ? sprintf("%0{$this->digit}d", $number) : $number;
        } else {
            return str_replace('?', $this->digit ? sprintf("%0{$this->digit}d", $number) : $number, $template);
        }
    }

    /**
     * Creates a query instance with the given condition.
     * @param string $targetClass the target AR class
     * @param mixed $condition query condition
     * @return \yii\db\ActiveQueryInterface the query instance
     */
    protected function createQuery($targetClass, $condition)
    {
        /* @var $targetClass \yii\db\ActiveRecordInterface */
        $query = $targetClass::find()->where($condition);
        if ($this->filter instanceof \Closure) {
            call_user_func($this->filter, $query);
        } elseif ($this->filter !== null) {
            $query->andWhere($this->filter);
        }

        return $query;
    }
}