<?php
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use backend\models\MoveSurplusForm;

$this->title = '商品库存变化记录 - ' . $model->name;
?>
<p>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-move"><i class="fa fa-pencil"></i> 变更库存</button>
</p>
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
<div class="modal fade" id="modal-move">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'action' => ['/goods/move-surplus', 'id' => $model->id],
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label' => 'col-md-2',
                        'wrapper' => 'col-md-8',
                        'hint' => ''
                    ]
                ]
            ]) ?>
            <?php $moveSurplusForm = new MoveSurplusForm($model->id) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">变更库存</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($moveSurplusForm, 'amount')->hint('请输入一个不等于0的整数，库存将根据您的变更值进行<strong>偏移</strong>而不是替换。') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">确认</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>