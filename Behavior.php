<?php

namespace mdm\autonumber;

use yii\db\StaleObjectException;

/**
 * Description of AutoNumber
 *
 * @author MDMunir
 */
class Behavior extends \yii\behaviors\AttributeBehavior
{

	public $digit;

	protected function getValue($event)
	{
		$value = parent::getValue($event);

		do {
			$repeat = false;
			try {
				$ar = AutoNumber::find($value);
				if ($ar) {
					$number = $ar->auto_number + 1;
				} else {
					$ar = new AutoNumber;
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