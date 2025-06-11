<?php

namespace app\models\search;

use app\enums\TrackStatus;
use app\models\Track;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TrackSearch extends Track
{
    /**
     * Правила валидации для параметров поиска.
     */
    public function rules()
    {
        return [
            [['track_number', 'status'], 'safe'],
            ['status', 'in', 'range' => array_map(fn ($case) => $case->value, TrackStatus::cases()), 'skipOnEmpty' => true],
        ];
    }

    /**
     * Сценарии, если требуется.
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Выполняет поиск треков на основе переданных параметров.
     *
     * @param array $params Параметры запроса
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Track::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->status !== null && $this->status !== '') {
            $query->andWhere(['status' => $this->status]);
        }

        if ($this->track_number !== null && $this->track_number !== '') {
            $query->andWhere(['like', 'track_number', $this->track_number]);
        }

        return $dataProvider;
    }
}