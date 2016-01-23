<?php
use yii\grid\GridView;

$this->title = '商品库存变化记录 - ' . $model->name;
?>
<div class="row">
    <div class="col-lg-12">
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover table-bordered'],
                'summaryOptions' => ['tag' => 'p', 'class' => 'text-right text-info'],
                'columns' => [
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:Y-m-d H:i'],
                        'headerOptions' => ['class' => 'col-sm-2 text-center'],
                        'contentOptions' => ['class' => 'text-center']
                    ],
                    [
                        'attribute' => 'surplus_before',
                        'headerOptions' => ['class' => 'col-sm-2 text-center'],
                        'contentOptions' => ['class' => 'text-center']
                    ],
                    [
                        'attribute' => 'amount',
                        'value' => function ($model, $key, $index, $column) {
                            return $model->amount > 0 ? '+' . $model->amount : $model->amount;
                        },
                        'headerOptions' => ['class' => 'col-sm-2 text-center'],
                        'contentOptions' => ['class' => 'text-center']
                    ],
                    [
                        'attribute' => 'surplus_after',
                        'headerOptions' => ['class' => 'col-sm-2 text-center'],
                        'contentOptions' => ['class' => 'text-center']
                    ],
                    [
                        'attribute' => 'remark',
                        'headerOptions' => ['class' => 'col-sm-4 text-center'],
                        'contentOptions' => ['class' => 'text-center']
                    ]
                ]
        ]) ?>
    </div>
</div>
