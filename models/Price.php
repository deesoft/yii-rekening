<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%price}}".
 *
 * @property integer $id
 * @property string $group
 * @property string $date_from
 * @property double $subscription
 * @property integer $meter_min
 * @property double $price
 * @property integer $threshold_1
 * @property double $price_1
 * @property integer $threshold_2
 * @property double $price_2
 *
 * @property Invoice[] $invoices
 */
class Price extends \yii\db\ActiveRecord
{
    const GROUP_BASIC = 'BASIC';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%price}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_from'], 'required'],
            [['date_from'], 'safe'],
            [['subscription', 'price', 'price_1', 'price_2'], 'number'],
            [['meter_min', 'threshold_1', 'threshold_2'], 'integer'],
            [['group'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => 'Group',
            'date_from' => 'Date From',
            'subscription' => 'Subscription',
            'meter_min' => 'Meter Min',
            'price' => 'Price',
            'threshold_1' => 'Threshold 1',
            'price_1' => 'Price 1',
            'threshold_2' => 'Threshold 2',
            'price_2' => 'Price 2',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['price_id' => 'id']);
    }

    /**
     *
     * @param Periode $periode
     * @param int $meter
     * @return array
     */
    public static function calculate($periode, $meter, $group = self::GROUP_BASIC)
    {
        /* @var $price Price */
        $price = static::find()->where(['<=', 'date_from', $periode->date_from])
            ->andWhere(['group' => $group])
            ->orderBy(['date_from' => SORT_DESC])
            ->one();

        $meter1 = $meter;
        if ($price->threshold_1 > 0 && $meter1 > $price->threshold_1) {
            $meter2 = $meter1 - $price->threshold_1;
            $meter1 = $price->threshold_1;
        } else {
            $meter2 = 0;
        }
        if ($price->threshold_2 > 0 && $meter2 > $price->threshold_2) {
            $meter3 = $meter2 - $price->threshold_2;
            $meter2 = $price->threshold_2;
        } else {
            $meter3 = 0;
        }
        $result = [
            'id' => $price->id,
            'subscription' => $price->subscription,
            'meter_min' => $price->meter_min,
            'price' => $price->price,
            'price2' => $price->price_1,
            'price3' => $price->price_2,
            'meter' => $meter,
            'meter1' => $meter1,
            'meter2' => $meter2,
            'meter3' => $meter3,
            'ammount1' => ($meter1 > $price->meter_min ? $meter1 : $price->meter_min) * $price->price,
            'ammount2' => $meter2 * $price->price_1,
            'ammount3' => $meter3 * $price->price_2,
        ];
        $result['total'] = $result['subscription'] + $result['ammount1'] + $result['ammount2'] + $result['ammount3'];

        return $result;
    }
}
