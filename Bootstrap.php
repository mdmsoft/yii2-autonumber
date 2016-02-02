<?php

namespace mdm\autonumber;

use yii\base\BootstrapInterface;
use yii\validators\Validator;

/**
 * Description of Bootstrap
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Validator::$builtInValidators['nextValue'] = __NAMESPACE__ . '\AutonumberValidator';
        Validator::$builtInValidators['autonumber'] = __NAMESPACE__ . '\AutonumberValidator';
    }
}