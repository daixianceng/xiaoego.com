<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\JsExpression;
use common\models\Apply;
use common\models\Store;
use kartik\date\DatePicker;
use kartik\select2\Select2;

$this->title = '采购管理';
?>
<p>
    <button type="button" class="btn btn-info btn-status" value="<?= Apply::STATUS_PENDING ?>">申请中</button>
    <button type="button" class="btn btn-success btn-status" value="<?= Apply::STATUS_PASSED ?>">通过</button>
    <button type="button" class="btn btn-danger btn-status" value="<?= Apply::STATUS_REJECTED ?>">驳回</button>
    <button type="button" class="btn btn-warning btn-status" value="<?= Apply::STATUS_CANCELLED ?>">取消</button>
    <button type="button" class="btn btn-default btn-status" value="<?= Apply::STATUS_COMPLETED ?>">完成</button>
</p>
<div class="row">
    <div class="col-lg-12">
        <?php Pjax::begin() ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-striped table-bordered table-center'],
                'summaryOptions' => ['tag' => 'p', 'class' => 'text-right text-info'],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    $class = '';
                    switch ($model->status) {
                        case Apply::STATUS_PENDING :
                            $class = 'info';
                            break;
                        case Apply::STATUS_PASSED :
                            $class = 'success';
                            break;
                        case Apply::STATUS_REJECTED :
                            $class = 'danger';
                            break;
                        case Apply::STATUS_CANCELLED :
                            $class = 'warning';
                            break;
                        case Apply::STATUS_COMPLETED :
                            $class = 'default';
                            break;
                        default :
                            break;
                    }
                    
                    return ['class' => $class];
                },
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'col-md-1']
                    ],
                    [
                        'attribute' => 'apply_sn',
                        'headerOptions' => ['class' => 'col-md-2'],
                        'filterInputOptions' => ['class' => 'form-control input-sm']
                    ],
                    [
                        'attribute' => 'store_id',
                        'headerOptions' => ['class' => 'col-md-4'],
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                        'value' => function ($model, $key, $index, $column) {
                            return $model->store->school->name . '-' . $model->store->name;
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'initValueText' => ($store = Store::findOne($searchModel->store_id)) ? $store->name : '' ,
                            'attribute' => 'store_id',
                            'size' => Select2::SMALL,
                            'theme' => Select2::THEME_KRAJEE,
                            'options' => ['placeholder' => '搜索店铺名称...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'ajax' => [
                                    'url' => Url::to(['/store/name-filter']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function (store) { return store.text; }'),
                                'templateSelection' => new JsExpression('function (store) { return store.text; }'),
                            ]
                        ]),
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Apply::getStatusList(),
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                        'headerOptions' => ['class' => 'col-md-1'],
                        'value' => function ($model, $key, $index, $column) {
                            return $model->statusMsg;
                        }
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
                        'headerOptions' => ['class' => 'col-md-2'],
                        'template' => '{view}'
                    ]
                ]
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>
<?php
$script = <<<SCRIPT
$('.btn-status').click(function() {
    $('[name="ApplySearch[status]"]').val($(this).val()).change();
});
SCRIPT;
$this->registerJs($script);