<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    public function rules()
    {
        return [
            [['id', 'status_id', 'role'], 'integer'],
            [['username', 'created_date', 'email'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        // load the seach form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['role' => $this->role]);
        $query->andFilterWhere(['status_id' => $this->status_id]);
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'creation_date', $this->created_date])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
} 