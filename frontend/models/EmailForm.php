<?php

namespace frontend\models;

use Yii;
use yii\base\Exception;
use yii\mail\MessageInterface;
use yii\web\UploadedFile;

/**
 * EmailForm.
 */
class EmailForm extends Email
{
    public $attachment;

    /**
     * Save email and attachments
     * @return bool
     */
    public function mailSave()
    {
        $basePath = Yii::$app->params['mailPath'];
        $files = UploadedFile::getInstances($this, 'attachment');
        $flag = $this->save();

        if (!is_dir($basePath . $this->id))
            mkdir($basePath . $this->id);

        if (isset($files)) {
            foreach ($files as $file) {
                $extension = $file->getExtension();
                $filename = $file->getBaseName() . '.' . $extension;
                $fileType = $this->getFileType($extension);
                $file->saveAs($basePath. $this->id .'/' . $filename);
                $attachment = new Attachment();
                $attachment->email_id = $this->id;
                $attachment->caption = $filename;
                $attachment->downloadUrl = $basePath. $this->id .'/' . $filename;
                $attachment->size = $file->size;
                $attachment->type = $fileType;
                $attachment->save();
            }
        }

        return $flag;
    }

    /**
     * if is_deleted true when you click save, function deletes attachments.
     * @return bool
     */
    public function deleteAttachments() {

        $deletedAttachments = Attachment::findAll(['email_id' => $this->id, 'is_deleted' => true]);

        if(count($deletedAttachments) > 0) {
            foreach ($deletedAttachments as $deletedAttachment) {
                unlink($deletedAttachment->downloadUrl);
                $deletedAttachment->delete();
            }
        }

        return true;
    }

    /**
     * changes extensions to type for preview.
     * $type http://plugins.krajee.com/file-input/plugin-options#previewSettings
     * @param $extension
     * @return string
     */
    public function getFileType($extension)
    {
        switch ($extension)
        {
            case $extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'gif':
                $type =  'image';
                break;
            case $extension == 'html':
                $type = 'html';
                break;
            case $extension == 'txt':
                $type = 'text';
                break;
            case $extension == 'pdf':
                $type = 'pdf';
                break;
            case $extension == 'mp4' || $extension == 'wmv' || $extension == 'flv':
                $type = 'video';
                break;
            case $extension == 'mp3' || $extension == 'aac' || $extension == 'wav':
                $type = 'audio';
                break;
            default:
                $type = 'other';
                break;
        }

        return $type;
    }


    /**
     * Email Send
     * Creates a temporary folder for send unsaved attachments
     * @param null $id
     * @return MessageInterface
     * @throws Exception
     */

    public function sendEmail($id = null)
    {
        $basePath = Yii::$app->params['tempPath'];
        if($id != null)
            $attachments = Attachment::findAll(['email_id' => $id, 'is_deleted' => false]);

        $files = UploadedFile::getInstances($this,'attachment');
        $message = Yii::$app->mailer->compose();
        if (empty($files) && $id == null) {
            $message->setTo($this->email);
            $message->setFrom([Yii::$app->params['senderEmail'] => $this->name]);
            $message->setSubject($this->subject);
            $message->setHtmlBody($this->body);
            $message->send();
        } else {

            $tmpFolderName = Yii::$app->security->generateRandomString(10);
            mkdir($basePath . $tmpFolderName);
            $message->setTo($this->email);
            $message->setFrom([Yii::$app->params['senderEmail'] => $this->name]);
            $message->setSubject($this->subject);
            $message->setHtmlBody($this->body);

            if($attachments){
                foreach ($attachments as $attachment) {
                    $uploadedAttacment = $attachment->downloadUrl;
                    $message->attach($uploadedAttacment);
                }
            }

            foreach ($files as $file) {
                $filename = $file->getBaseName() . '.' . $file->getExtension();
                $filePath = $basePath . $tmpFolderName . '/'. $filename;
                $file->saveAs($filePath);
                $array[] = $filePath;
                $message->attach($filePath);
            }

            $message->send();
            if(!empty($array))
                foreach ($array as $element)
                    unlink($element);
            rmdir($basePath.$tmpFolderName);
        }
        return $message;
    }
}
