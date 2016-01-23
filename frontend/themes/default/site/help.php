<?php
use yii\helpers\Html;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
$this->title = '帮助中心';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content">
    <!-- heading -->
    <h2 class="br-orange default-head">订单相关</h2>
    <?= Collapse::widget([
        'items' => [
            [
                'label' => '在线支付支持什么付款方式？',
                'content' => '支付宝（Alipay）与微信支付，微信支付只可在微信浏览器中使用。',
                'options' => ['class' => 'panel-warning'],
                'contentOptions' => ['class' => 'in']
            ],
            [
                'label' => '货到付款支持什么付款方式？',
                'content' => '只支持现金。',
                'options' => ['class' => 'panel-warning'],
            ],
            [
                'label' => '能提供发票吗？',
                'content' => '很抱歉，暂时还不能提供发票。',
                'options' => ['class' => 'panel-warning'],
            ]
        ]
    ]) ?>
    <!-- heading -->
    <h2 class="br-orange default-head">关于配送</h2>
    <?= Collapse::widget([
        'items' => [
            [
                'label' => '成功下单后，多久能送到？',
                'content' => '在同一栋楼内，最快只需要5分钟即可送达。',
                'options' => ['class' => 'panel-warning'],
                'contentOptions' => ['class' => 'in']
            ],
            [
                'label' => '你们的送货范围是？',
                'content' => '同一个学校内的宿舍楼，建议选择您所在宿舍楼内（旁）的营业点来购买。',
                'options' => ['class' => 'panel-warning'],
            ],
            [
                'label' => '有配送费吗？',
                'content' => '配送免费！',
                'options' => ['class' => 'panel-warning'],
            ],
            [
                'label' => '可以退货吗？',
                'content' => '很抱歉，可以退换商品，不可退货。',
                'options' => ['class' => 'panel-warning'],
            ],
            [
                'label' => '可以上门取货吗？',
                'content' => '可以，去购买的营业点说明一下自己的收货信息或订单号来提取。',
                'options' => ['class' => 'panel-warning'],
            ]
        ]
    ]) ?>
    <!-- heading -->
    <h2 class="br-orange default-head">账户问题</h2>
    <?= Collapse::widget([
        'items' => [
            [
                'label' => '忘记密码怎么办？',
                'content' => '至登录页面，点击左下角忘记密码链接，进入自助服务。',
                'contentOptions' => ['class' => 'in'],
                'options' => ['class' => 'panel-warning'],
            ],
            [
                'label' => '如何更换手机号？',
                'content' => '进入“个人中心->手机”页面进行更换。',
                'options' => ['class' => 'panel-warning'],
            ]
        ]
    ]) ?>
</div>