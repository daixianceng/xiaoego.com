<?php
use yii\helpers\Html;
use dosamigos\chartjs\ChartJs;

$this->title = '营业点详情 - ' . Html::encode($model->name);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">营业点资料</div>
                    <div class="panel-body">
                        <p>手机：<?= $model->cellphone ?></p>
                        <p>地址：<?= Html::encode($model->address) ?></p>
                        <p>学校：<?= Html::encode($model->school->name) ?></p>
                        <p>状态：<?= $model->statusMsg ?></p>
                        <p>创建日期：<?= Yii::$app->formatter->asDatetime($model->created_at, "php:Y-m-d H:i:s") ?></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">统计数据</div>
                    <div class="panel-body">
                        <p>总订单量：<?= $countOrder ?></p>
                        <p>总完成订单量：<?= $countCompleted ?></p>
                        <p>总营业额：<?= $sumVolume ? $sumVolume : '0.00' ?>元</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default hidden-xs">
            <div class="panel-heading">近6个月营业额统计表（根据当月完成订单计算）</div>
            <div class="panel-body">
                <div>
                    <?= ChartJs::widget([
                        'type' => 'Bar',
                        'options' => [
                            'height' => 40,
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'tooltipTemplate' => '<%if (label){%><%=label%>: <%}%><%= value %>元'
                        ],
                        'data' => [
                            'labels' => $last6Month,
                            'datasets' => [
                                [
                                    'fillColor' => "rgba(151,187,205,0.5)",
                                    'strokeColor' => "rgba(151,187,205,0.8)",
                                    'highlightFill' => "rgba(151,187,205,0.75)",
                                    'highlightStroke' => "rgba(151,187,205,1)",
                                    'data' => $numDataVolumeMonth
                                ]
                            ]
                        ]
                    ]);?>
                </div>
            </div>
        </div>
        <div class="panel panel-default hidden-xs">
            <div class="panel-heading">近15天营业额统计表（根据当天完成订单计算）</div>
            <div class="panel-body">
                <div>
                    <?= ChartJs::widget([
                        'type' => 'Bar',
                        'options' => [
                            'height' => 40,
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'tooltipTemplate' => '<%if (label){%><%=label%>: <%}%><%= value %>元'
                        ],
                        'data' => [
                            'labels' => $last15days,
                            'datasets' => [
                                [
                                    'fillColor' => "rgba(151,187,205,0.5)",
                                    'strokeColor' => "rgba(151,187,205,0.8)",
                                    'highlightFill' => "rgba(151,187,205,0.75)",
                                    'highlightStroke' => "rgba(151,187,205,1)",
                                    'data' => $numDataVolume
                                ]
                            ]
                        ]
                    ]);?>
                </div>
            </div>
        </div>
        <div class="panel panel-default hidden-xs">
            <div class="panel-heading">近15天订单完成统计表（昨天生成今天完成的订单则计入今天）</div>
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
                                    'data' => $numDataCompleted
                                ]
                            ]
                        ]
                    ]);?>
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