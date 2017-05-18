<?php

namespace app\classes;

/**
 * Description of Formatter
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Formatter extends \yii\i18n\Formatter
{
    public function asMonthText($value)
    {
        list($y,$m) = [(int)($value/12), $value % 12 + 1];
        return "$m/$y";
    }
}
