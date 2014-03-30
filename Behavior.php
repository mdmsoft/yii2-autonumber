<?php

namespace mdm\autonumber;

use yii\db\StaleObjectException;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;

/**
 * Description of AutoNumber
 *
 * @author MDMunir
 */
class Behavior extends \yii\behaviors\AttributeBehavior
{

	public $digit;
	public $group;
	public $attribute;


	public function init()
	{
		if($this->group === null){
			throw new InvalidConfigException('property group ');
		}
		if($this->attribute !== null){
			$this->attributes[BaseActiveRecord::EVENT_BEFORE_INSERT][] = $this->attribute;
		}
		parent::init();
	}

	protected function getValue($event)
	{
		$value = parent::getValue($event);

		do {
			$repeat = false;
			try {
				$ar = AutoNumber::find([
					'template_group'=>  $this->group,
					'template_num'=>$value,
				]);
				if ($ar) {
					$number = $ar->auto_number + 1;
				} else {
					$ar = new AutoNumber;
					$ar->template_group = $this->group;
					$ar->template_num = $value;
					$number = 1;
				}
				$ar->update_time = time();
				$ar->auto_number = $number;
				$ar->save();
			} catch (\Exception $exc) {
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