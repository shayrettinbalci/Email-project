<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Email */

$this->title = 'Update Email: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'attachmentInitialPreviews' => $attachmentInitialPreviews,
        'attachmentInitialPreviewConfigs' => $attachmentInitialPreviewConfigs
    ]) ?>

</div>