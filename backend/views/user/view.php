<?php
use yii\helpers\Html;
use common\models\Order;
use dosamigos\chartjs\ChartJs;

$this->title = '前台用户详情 - ' . Html::encode($model->mobile);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">用户资料</div>
                    <div class="panel-body">
                        <p>昵称：<?= empty($model->nickname) ? '（未设置）' : Html::encode($model->nickname) ?></p>
                        <p>性别：<?= $model->genderMsg ?></p>
                        <p>邮箱：<?= empty($model->email) ? '（未设置）' : $model->email ?></p>
                        <p>状态：<?= $model->statusMsg ?></p>
                        <p>注册日期：<?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">统计数据</div>
                    <div class="panel-body">
                        <p>总订单量：<?= Order::getCountByUserId($model->id) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default hidden-xs">
            <div class="panel-heading">近15天订单生成统计表</div>
            <div class="panel-body">
                <div>
                    <?= ChartJs::widget([
                        'type' => 'Line',
                        'options' => [
                            'height' => 40,
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'tooltipTemplate' => '<%if (label){%><%=label%>: <%}%><%= value %>个'
                        ],
                        'data' => [
                            'labels' => $last15days,
                            'datasets' => [
                                [
                                    'fillColor' => "rgba(151,187,205,0.5)",
                                    'strokeColor' => "rgba(151,187,205,1)",
                                    'pointColor' => "rgba(151,187,205,1)",
                                    'pointStrokeColor' => "#fff",
                                    'data' => $numDataOrder
                                ]
                            ]
                        ]
                    ]);?>
                </div>
            </div>
        </div>
    </div>
</div>