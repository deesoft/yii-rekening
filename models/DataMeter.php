<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%data_meter}}".
 *
 * @property integer $id
 * @property integer $periode_id
 * @property integer $customer_id
 * @property integer $meter
 *
 * @property Customer $customer
 * @property Periode $periode
 */
class DataMeter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%data_meter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['periode_id', 'customer_id', 'meter'], 'required'],
            [['periode_id', 'customer_id', 'meter'], 'integer'],
            //[['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'month' => 'Month',
            'customer_id' => 'Customer ID',
            'meter' => 'Meter',
        ];
    }

    /**
     *
     * @return Invoice
     */
    public function createInvoice()
    {
        $invoice = Invoice::findOne([
                'periode_id' => $this->periode_id,
                'customer_id' => $this->customer_id,
                'type' => Invoice::TYPE_METER,
        ]);
        if ($invoice === null) {
            $invoice = new Invoice([
                'periode_id' => $this->periode_id,
                'customer_id' => $this->customer_id,
                'type' => Invoice::TYPE_METER,
                'date' => date('Y-m-d'),
                'note' => "Tagihan bulan {$this->periode->name}",
            ]);
        }
        $periode = $this->periode;
        $prev = Periode::find()
            ->where(['<', 'date_from', $periode->date_from])
            ->orderBy(['date_from' => SORT_DESC])
            ->one();
        if ($prev && ($d = DataMeter::findOne(['periode_id' => $prev->id, 'customer_id' => $this->customer_id]))) {
            $meter0 = $d->meter;
        } else {
            $meter0 = 0;
        }
        $price = Price::calculate($periode, $this->meter-$meter0);
        $invoice->ammount = $price['total'];
        $invoice->component = $price;
        $invoice->ammount_component = json_encode($price);
        return $invoice->save() ? $invoice : false;
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
