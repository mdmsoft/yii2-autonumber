<?php

namespace mdm\autonumber;

use yii\db\StaleObjectException;
use yii\db\BaseActiveRecord;
use Exception;

/**
 * Description of AutoNumber
 *
 * @author MDMunir
 */
class Behavior extends \yii\behaviors\AttributeBehavior
{
    /**
     *
     * @var integer digit number of auto number
     */
    public $digit;

    /**
     *
     * @var mixed 
     */
    public $group;

    /**
     *
     * @var boolean 
     */
    public $unique = true;

    /**
     *
     * @var string 
     */
    public $attribute;

    public function init()
    {
        if ($this->attribute !== null) {
            $this->attributes[BaseActiveRecord::EVENT_BEFORE_INSERT][] = $this->attribute;
        }
        parent::init();
    }

    protected function getValue($event)
    {
        $value = parent::getValue($event);
        $group = md5(serialize([
            'class' => $this->unique ? get_class($this->owner) : false,
            'group' => $this->group
        ]));
        do {
            $repeat = false;
            try {
                $model = AutoNumber::findOne([
                        'group' => $group,
                        'template' => $value,
                ]);
                if ($model) {
                    $number = $model->number + 1;
                } else {
                    $model = new AutoNumber([
                        'group' => $group,
                        'template' => $value,
                    ]);
                    $number = 1;
                }
                $model->update_time = time();
                $model->number = $number;
                $model->save();
            } catch (Exception $exc) {
                if ($exc instanceof StaleObjectException) {
                    $repeat = true;
                } else {
                    throw $exc;
                }
            }
        } while ($repeat);
        return str_replace('?', $this->digit ? sprintf("%0{$this->digit}d", $number) : $number, $value);
    }
}