<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\web\JsExpression;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use common\models\Order;
use common\models\User;

$this->title = '订单列表';
?>
<p>
    <button type="button" class="btn btn-info btn-status" value="<?= Order::STATUS_UNPAID ?>">待付款</button>
    <button type="button" class="btn btn-danger btn-status" value="<?= Order::STATUS_UNSHIPPED ?>">未发货</button>
    <button type="button" class="btn btn-success btn-status" value="<?= Order::STATUS_SHIPPED ?>">配送中</button>
    <button type="button" class="btn btn-warning btn-status" value="<?= Order::STATUS_CANCELLED ?>">订单取消</button>
    <button type="button" class="btn btn-default btn-status" value="<?= Order::STATUS_COMPLETED ?>">订单完成</button>
    <button type="button" class="btn btn-purple btn-status" value="<?= Order::STATUS_DELETED ?>">订单删除</button>
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
                        case Order::STATUS_UNPAID :
                            $class = 'info';
                            break;
                        case Order::STATUS_UNSHIPPED :
                            $class = 'danger';
                            break;
                        case Order::STATUS_SHIPPED :
                            $class = 'success';
                            break;
                        case Order::STATUS_CANCELLED :
                            $class = 'warning';
                            break;
                        case Order::STATUS_DELETED :
                            $class = 'purple';
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
                        'attribute' => 'order_sn',
                        'headerOptions' => ['class' => 'col-md-2'],
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                    ],
                    [
                        'attribute' => 'user_id',
                        'headerOptions' => ['class' => 'col-md-2'],
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                        'value' => function ($model, $key, $index, $column) {
                            return $model->user->mobile;
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'initValueText' => ($user = User::findOne($searchModel->user_id)) ? $user->mobile : '' ,
                            'attribute' => 'user_id',
                            'size' => Select2::SMALL,
                            'theme' => Select2::THEME_KRAJEE,
                            'options' => ['placeholder' => '搜索用户手机...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'ajax' => [
                                    'url' => Url::to(['/order/user-mobile-filter']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function (user) { return user.text; }'),
                                'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                            ]
                        ]),
                    ],
                    [
                        'attribute' => 'payment',
                        'filter' => Order::getPaymentList(),
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                        'headerOptions' => ['class' => 'col-md-1'],
                        'value' => function ($model, $key, $index, $column) {
                            return $model->paymentMsg;
                        }
                    ],
                    [
                        'attribute' => 'real_fee',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'filterInputOptions' => ['class' => 'form-control input-sm', 'title' => '支持运算符'],
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Order::getStatusList(),
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                        'headerOptions' => ['class' => 'col-md-2'],
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
                        'headerOptions' => ['class' => 'col-md-2']
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'template' => '{view}',
                    ]
                ]
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>
<?php
$script = <<<SCRIPT
$('.btn-status').click(function() {
    $('[name="OrderSearch[status]"]').val($(this).val()).change();
});
setInterval(function () {
    $('.grid-view').yiiGridView('applyFilter');
}, 30000);
SCRIPT;
$this->registerJs($script);