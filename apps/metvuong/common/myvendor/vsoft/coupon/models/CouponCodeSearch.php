<?php

namespace vsoft\coupon\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use vsoft\coupon\models\CouponCode;

/**
 * CouponCodeSearch represents the model behind the search form about `vsoft\coupon\models\CouponCode`.
 */
class CouponCodeSearch extends CouponCode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cp_event_id', 'status', 'count', 'type', 'created_at', 'updated_at', 'amount_type'], 'integer'],
            [['amount'], 'integer', 'integerOnly' => false],
            [['code'], 'safe'],
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
        $query = CouponCode::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
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
            'cp_event_id' => $this->cp_event_id,
            'status' => $this->status,
            'count' => $this->count,
            'type' => $this->type,
            'amount' => $this->amount,
            'amount_type' => $this->amount_type,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
}
