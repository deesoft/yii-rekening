<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DataMeter;

/**
 * DataMeterSearch represents the model behind the search form about `app\models\DataMeter`.
 */
class DataMeterSearch extends DataMeter
{

    public function formName()
    {
        return '';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'periode_id', 'customer_id', 'meter'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = DataMeter::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'periode_id' => $this->periode_id,
            'customer_id' => $this->customer_id,
            'meter' => $this->meter,
        ]);

        return $dataProvider;
    }
}
