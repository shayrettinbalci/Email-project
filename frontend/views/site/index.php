<?php

/* @var $this yii\web\View */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;


$this->title = 'Index';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(); ?>
            <?= GridView::widget([
                'dataProvider'=> $dataProvider,
                'filterModel' => $searchModel,
                'options' => [ 'style' => 'table-layout:fixed;' ],
                'columns' =>
                    [['class' => 'kartik\grid\SerialColumn'],
                        'name',
                        'email:email',
                        'subject',
                        'body:html',
                        ['class' =>
                            'kartik\grid\ActionColumn',
                            'viewOptions' => ['hidden' => true,],
                            'buttons' => [ 'Update' => function ($url) {
                                return Html::a('<span class="fa fa-pencil"></span>', $url, ['title' => 'update']);
                            },
                                ],
                            'urlCreator' => function ($action, $model) {
                                return Url::to(['email/'.$action, 'id' => $model->id]);
                            }
                            ]

                    ],
                'persistResize' => true,
                'bordered' => true,
                'striped' => true,
                'condensed' => true,
                'responsive' => true,
                'hover' => true,
            ]);?>
            <?php ActiveForm::end(); ?>
        </div>

    </div>

    <!--    <code>--><?//= __FILE__ ?><!--</code>-->
</div>
