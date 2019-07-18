<?php

namespace frontend\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "email".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $subject
 * @property string $body
 */


class Email extends ActiveRecord
{

    public static function tableName()
    {
        return 'email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'subject', 'body'], 'required'],
            [['email'], 'email'],
            [['body'], 'string'],
            [['name', 'email', 'subject'], 'string', 'max' => 255]
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'subject' => 'Subject',
            'body' => 'Body',
        ];
    }

    /**
     * Foreign key for attachments one-to-many
     * @return ActiveQuery
     */
    public function getAttachments()
    {
        return $this->hasMany(Attachment::className(), ['email_id' => 'id']);
    }
}
