<?php

namespace mdm\autonumber;

/**
 * This is the model class for table "auto_number".
 *
 * @property string $group
 * @property string $template
 * @property integer $number
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AutoNumber extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auto_number}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['optimistic_lock', 'number'], 'default', 'value' => 1],
            [['group'], 'required'],
            [['number'], 'integer'],
            [['group'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'template' => 'Template Num',
            'number' => 'Number',
        ];
    }

    /**
     * @inheritdoc
     */
    public function optimisticLock()
    {
        return 'optimistic_lock';
    }

    /**
     * 
     * @param string $format value inner `{}` will evaluate as date. Number of digit represented as number of `?`.
     * @param bool $alnum if true will generate alfanumeric value. If false generate only number.
     * @param int $digit For compatibility purpose.
     * @param array $group For compatibility purpose.
     * @return string
     */
    public static function generate($format, $alnum = false, $digit = null, array $group = [])
    {
        if ($format) {
            $format = preg_replace_callback('/\{([^\}]+)\}/', function($matchs) {
                return date($matchs[1]);
            }, $format);
        }

        if (empty($group) && strlen($format) < 32) {
            $key = (string)$format;
        } else {
            $group['value'] = $format;
            $key = md5(serialize($group));
        }

        $command = Yii::$app->db->createCommand();
        $command->setSql('SELECT [[number]] FROM {{auto_number}} WHERE [[group]]=:key');
        $counter = $command->bindValue(':key', $key)->queryScalar() + 1;
        $command->upsert('auto_number', ['group' => $key, 'number' => $counter, 'optimistic_lock' => 1, 'update_time' => time()])->execute();
        $number = $alnum ? strtoupper(base_convert($counter, 10, 36)) : (string) $counter;

        if ($format === null) {
            $result = $number;
        } elseif ($digit) {
            $number = str_pad($number, $digit, '0', STR_PAD_LEFT);
            $result = str_replace('?', $number, $format);
        } else {
            $places = [];
            $total = 0;
            $result = preg_replace_callback('/\?+/', function($matchs) use(&$places, &$total) {
                $n = strlen($matchs[0]);
                $i = count($places);
                $places[] = $n;
                $total += $n;
                return "<[~{$i}~]>";
            }, $format);

            if ($total > 1) {
                $number = str_pad($number, $total, '0', STR_PAD_LEFT);
                $parts = [];
                for ($i = count($places) - 1; $i >= 0; $i--) {
                    if ($i == 0) {
                        $parts[0] = $number;
                    } else {
                        $parts[$i] = substr($number, -$places[$i]);
                        $number = substr($number, 0, -$places[$i]);
                    }
                }
                $result = preg_replace_callback('/<\[~(\d+)~\]>/', function($matchs) use(&$parts) {
                    $i = $matchs[1];
                    return $parts[$i];
                }, $result);
            } else {
                $result = str_replace('?', $number, $format);
            }
        }
        return $result;
    }
}
