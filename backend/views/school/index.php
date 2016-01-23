<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\date\DatePicker;

$this->title = '学校列表';
?>
<p>
    <?= Html::a('<i class="fa fa-plus"></i> 添加学校', ['school/add'], ['class' => 'btn btn-primary']) ?>
</p>
<div class="row">
    <div class="col-lg-12">
        <?php Pjax::begin() ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-striped table-bordered table-center'],
                'summaryOptions' => ['tag' => 'p', 'class' => 'text-right text-info'],
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'col-md-1']
                    ],
                    [
                        'attribute' => 'name',
                        'headerOptions' => ['class' => 'col-md-7'],
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:Y-m-d H:i'],
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'attribute' => 'date',
                            'options' => ['class' => 'input-sm'],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]),
                        'headerOptions' => ['class' => 'col-md-3']
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'template' => '{update}'
                    ]
                ]
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>