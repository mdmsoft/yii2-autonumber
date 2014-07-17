<?php

namespace mdm\autonumber;

use yii\helpers\ArrayHelper;
use yii\validators\Validator;

/**
 * Description of Bootstrap
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Bootstrap implements \yii\base\BootstrapInterface
{

    public function bootstrap($app)
    {
        $name = ArrayHelper::getValue($app->params, 'mdm.autonumber.validator', 'nextValue');
        if (!empty($name)) {
            Validator::$builtInValidators[$name] = __NAMESPACE__ . '\NextValueValidator';
        }
    }
}