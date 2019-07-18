<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use kartik\file\FileInput;
use yii\web\JqueryAsset;

?>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/js/sendEmail.js',
        ['depends' => [JqueryAsset::className()]
    ]); ?>

<div class="email-form">
 <div class="loader">
    <div class="row">
        <div class="col-lg-10">

            <?php $form = ActiveForm::begin([
                'id' => 'email-form',
                'options' => ['enctype' => 'multipart/form-data'
                ]]); ?>

            <?= Html::hiddenInput("emailId", $model->id) ?>

            <?= $form->field($model, 'name')->textInput() ?>

            <?= $form->field($model, 'email') ?>

            <?= $form->field($model, 'subject') ?>

            <?= $form->field($model, 'body')->widget(CKEditor::className(),
                [
                    'preset' => 'full',
                    'clientOptions' => [
                        'filebrowserUploadUrl' => 'http://frontend.test/yii-application/frontend/web/index.php?r=email%2Fupload',
                        'filebrowserBrowseUrl' => '',
                        'extraPlugins' => [
                            'colorbutton',
                            'emoji',
                            'font',
                            'iframe',
                            'preview',
                            'div',
                            'print',
                            'find',
                            'forms',
                            'justify',
                            'newpage',
                            'showblocks',
                        ],
                        'allowedContent' => true
                    ],
                ]) ?>

            <?=$form->field($model, 'attachment')->widget(FileInput::classname(),
                [
                    'options' => ['multiple' => true],
                    'pluginOptions' => [
                        'showUpload' => false,
                        'initialPreview' => $attachmentInitialPreviews,
                        'initialPreviewAsData'=>true,
                        'initialPreviewConfig' => $attachmentInitialPreviewConfigs,
                        'deleteUrl' => 'index.php?r=email/file-delete',
                        'overwriteInitial' => false
                    ]
                ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?= Html::button('Test', ['class' => 'btn btn-primary','id' => 'send-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
 </div>

</div>
