<?php

namespace app\models;

use app\enums\TrackStatus;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "tracks".
 *
 * @property int $id
 * @property string $track_number
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 */
class Track extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tracks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => TrackStatus::NEW->value],
            [['track_number'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            [['track_number'], 'string', 'max' => 50],
            ['status', 'in', 'range' => array_map(fn ($case) => $case->value, TrackStatus::cases())],
            [['track_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'track_number' => 'Track Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('CURRENT_TIMESTAMP'),
            ],
        ];
    }
}
