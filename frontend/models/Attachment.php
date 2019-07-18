<?php

namespace frontend\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "attachment".
 *
 * @property int $key
 * @property int $email_id
 * @property string $caption
 * @property string $downloadUrl
 * @property string $size
 * @property string $type
 * @property int $is_deleted
 */
class Attachment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attachment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email_id', 'caption', 'downloadUrl', 'size', 'type'], 'required'],
            [['email_id', 'size', 'is_deleted'], 'integer'],
            [['caption', 'downloadUrl', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => 'Key',
            'email_id' => 'Email ID',
            'caption' => 'Caption',
            'downloadUrl' => 'Download Url',
            'size' => 'Size',
            'type' => 'Type',
            'is_deleted' => 'Is Deleted',
        ];
    }


    /**
     * Foreign key from email one-to-many
     * @return ActiveQuery
     */
    public function getEmail()
    {
        return $this->hasOne(Email::className(), ['id' => 'email_id']);
    }

}
