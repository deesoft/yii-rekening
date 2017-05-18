<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%invoice}}".
 *
 * @property integer $id
 * @property string $date
 * @property integer $periode_id
 * @property integer $customer_id
 * @property string $type
 * @property string $note
 * @property integer $price_id
 * @property double $ammount
 * @property string $ammount_component
 *
 * @property Price $price
 * @property Customer $customer
 * @property Periode $periode
 */
class Invoice extends \yii\db\ActiveRecord
{
    const TYPE_METER = 'METER';

    public $component;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'periode_id', 'customer_id', 'ammount'], 'required'],
            [['date', 'type', 'note'], 'safe'],
            [['periode_id', 'customer_id', 'price_id'], 'integer'],
            [['ammount'], 'number'],
            [['ammount_component'], 'string'],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Price::className(), 'targetAttribute' => ['price_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'periode_id' => 'Periode',
            'customer_id' => 'Customer ID',
            'price_id' => 'Price ID',
            'ammount' => 'Ammount',
            'ammount_component' => 'Ammount Component',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(Price::className(), ['id' => 'price_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriode()
    {
        return $this->hasOne(Periode::className(), ['id' => 'periode_id']);
    }
}
