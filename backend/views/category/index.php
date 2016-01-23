<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use himiklab\sortablegrid\SortableGridView;
use common\models\Category;

$this->title = '商品分类列表';
?>
<p>
    <?= Html::a('<i class="fa fa-plus"></i> 添加商品分类', ['category/add'], ['class' => 'btn btn-primary']) ?>
</p>
<div class="row">
    <div class="col-lg-12">
        <?php Pjax::begin() ?>
        <?= SortableGridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'sortableAction' => ['/category/sort'],
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
                        'attribute' => 'slug',
                        'headerOptions' => ['class' => 'col-md-2'],
                        'filterInputOptions' => ['class' => 'form-control input-sm'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['class' => 'col-md-2'],
                        'template' => '{update}'
                    ]
                ]
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>