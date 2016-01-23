<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\date\DatePicker;
use common\models\Goods;
use common\models\Category;
use common\models\Purchase;

$this->title = '商品列表';

$statusList = Goods::getStatusList();
unset($statusList[Goods::STATUS_DELETED]);
?>
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
                    ],
                    [
                        'attribute' => 'name',
                        'headerOptions' => ['class' => 'col-md-2'],
                        'filterInputOptions' => ['class' => 'form-control input-sm']
                    ],
                    [
                        'attribute' => 'category_id',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'filter' => Category::getKeyValuePairs(),
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                        'value' => function ($model, $key, $index, $column) {
                            return $model->category->name;
                        }
                    ],
                    [
                        'attribute' => 'price',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'format' => 'html',
                        'filterInputOptions' => ['class' => 'form-control input-sm', 'title' => '支持运算符'],
                        'value' => function ($model, $key, $index, $column) {
                            return '&yen; ' . $model->price;
                        }
                    ],
                    [
                        'attribute' => 'surplus',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'filterInputOptions' => ['class' => 'form-control input-sm', 'title' => '支持运算符']
                    ],
                    [
                        'attribute' => 'sales',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'filterInputOptions' => ['class' => 'form-control input-sm', 'title' => '支持运算符']
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'filter' => $statusList,
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                        'headerOptions' => ['class' => 'col-md-1'],
                        'value' => function ($model, $key, $index, $column) {
                            return Html::dropDownList('status', $model->status, $column->filter, ['data-id' => $model->id]);
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
                        'header' => '采购',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'template' => '{purchase}',
                        'buttons' => [
                            'purchase' => function ($url, $model, $key) {
                                return Purchase::hasGoods($model->id) ? Html::button('已加入', ['class' => 'btn btn-default btn-xs btn-purchase active', 'data-id' => $model->id]) : Html::button('加入', ['class' => 'btn btn-default btn-xs btn-purchase', 'data-id' => $model->id]);
                            },
                        ]
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['class' => 'col-md-1'],
                        'template' => '{update} {surplus} {delete}',
                        'buttons' => [
                            'surplus' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-equalizer" aria-hidden="true"></span>', $url, ['title' => '库存变化记录']);
                            },
                        ]
                    ]
                ]
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>
<?php
$url = Url::to(['/goods/status']);
$urlAdd = Url::to(['/purchase/add']);
$urlDelete = Url::to(['/purchase/delete']);
$js = <<<JS
var handle = function () {
    var id = $(this).attr('data-id');
    var status = $(this).val();
    $.ajax({
        url: '{$url}?id=' + id ,
        type: 'post',
        dataType: 'json',
        data: {status: status},
        success: function () {},
        error: function () {}
    });
};
$('select[name="status"]').change(handle);

var toggle = function () {
    if (pending) return;
    var \$this = $(this);
    var id = \$this.attr('data-id');
    pending = true;
    if (\$this.hasClass('active')) {
        $.ajax({
            url: '{$urlDelete}?id=' + id ,
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.status === 'success') {
                    \$this.removeClass('active').text('加入');
                } else {
                    alert(data.data.message);
                }
            },
            error: function () {},
            complete: function () {
                pending = false;
            }
        });
    } else {
        $.ajax({
            url: '{$urlAdd}?id=' + id ,
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.status === 'success') {
                    \$this.addClass('active').text('已加入');
                } else {
                    alert(data.data.message);
                }
            },
            error: function () {},
            complete: function () {
                pending = false;
            }
        });
    }
};
var pending = false;
$('.btn-purchase').click(toggle);

$(document).on('pjax:complete', function() {
    $('select[name="status"]').change(handle);
    $('.btn-purchase').click(toggle);
})
JS;

$this->registerJs($js);
?>