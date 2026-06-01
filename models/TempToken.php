<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Модель для временных токенов
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property integer $created_at
 * @property integer $updated_at
 */
class TempToken extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%temp_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token'], 'required'],
            [['user_id'], 'integer'],
            [['token'], 'string', 'length' => 32],
            [['token'], 'unique'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id', 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Связь с пользователем
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Удаляет токен
     */
    public function deleteToken()
    {
        return $this->delete();
    }

    /**
     * Удаляет все токены пользователя
     */
    public static function deleteUserTokens($userId)
    {
        return static::deleteAll(['user_id' => $userId]);
    }
}
