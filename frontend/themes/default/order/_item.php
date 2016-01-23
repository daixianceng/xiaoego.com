<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Order;

/* @var $model \common\models\Order */
?>
<div class="order-item">
    <h4 class="br-red">订单号：<?= $model->order_sn ?><span><?= $model->statusMsg ?></span></h4>
    <div class="order-body">
        <a class="order-link" href="<?= Url::to(['/order/detail', 'order' => $model->order_sn]) ?>">
            <p><?= Html::encode($model->store->name) ?></p>
            <p>&yen; <?= $model->real_fee ?></p>
            <p><?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
            <div class="order-go">
                <i class="fa fa-chevron-right"></i>
            </div>
        </a>
    </div>
</div>