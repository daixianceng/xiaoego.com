<?php
use yii\helpers\Html;
use common\helpers\Url;
use common\models\Order;
use frontend\assets\CountdownAsset;

/* @var $this \yii\web\View */

if ($model->status === Order::STATUS_UNPAID) {
    CountdownAsset::register($this);
}

$this->title = '订单详情';
$this->params['breadcrumbs'][] = ['label' => '个人中心', 'url' => ['/i']];
$this->params['breadcrumbs'][] = ['label' => '我的订单', 'url' => ['/order']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-header">
    <?php if ($model->status === Order::STATUS_SHIPPED) :?>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-receive"><i class="fa fa-tag"></i> 确认收货</button>
    <?php endif;?>
    <?php if ($model->status === Order::STATUS_UNPAID) :?>
        <?= Html::a('<i class="fa fa-credit-card"></i> 立即支付 (<span class="countdown"></span>)', ['/order/pay', 'order' => $model->order_sn], ['class' => 'btn btn-primary']) ?>
        <button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#modal-cancel"><i class="fa fa-times"></i> 取消订单</button>
    <?php endif;?>
    <?php if (in_array($model->status, [Order::STATUS_CANCELLED, Order::STATUS_COMPLETED])) :?>
        <button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#modal-del"><i class="fa fa-trash"></i> 删除订单</button>
    <?php endif;?>
    <div class="clearfix"></div>
</div>
<div class="orderview">
    <h4>订单号：<?= $model->order_sn ?><span class="hidden-xs"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></span></h4>
    <div class="orderview-body">
        <p>购买店铺：<?= Html::encode($model->store->name) ?></p>
        <p>订单状态：<?= $model->statusMsg ?><?= $model->cancelled_msg ? '，取消原因：' . Html::encode($model->cancelled_msg) : '' ?></p>
        <p>送达时间：<?= $model->bookTimeMsg ?></p>
        <p>支付方式：<?= $model->paymentMsg ?></p>
        <p>应付款：&yen; <?= $model->real_fee ?> <?= $model->preferentialPrettyMsg ?>  <?= $model->newDownMsg ?></p>
        <p>订单描述：<?= Html::encode($model->description) ?></p>
        <?php if (!empty($model->remark)) :?>
        <p>备注：<?= Html::encode($model->remark) ?></p>
        <?php endif;?>
    </div>
</div>
<div class="ordergoods">
    <?php foreach ($model->goods as $key => $goods) :?>
    <div class="ordergoods-item">
        <table class="table">
            <tbody>
                <tr>
                    <td class="col-sm-1"><?= Html::img(Url::toCover($goods->cover), ['class' => 'img-responsive img-rounded']) ?></td>
                    <td class="col-sm-3"><?= Html::encode($goods->name) ?></td>
                    <td class="col-sm-3"><?= Html::encode($goods->category) ?></td>
                    <td class="col-sm-2"><?= $goods->count ?> <?= Html::encode($goods->unit) ?></td>
                    <td class="col-sm-3">&yen; <?= $goods->price ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endforeach;?>
</div>
<?php if ($model->status === Order::STATUS_SHIPPED) :?>
<div class="modal fade" id="modal-receive">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['/order/receive', 'order' => $model->order_sn]) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">确认收货</h4>
            </div>
            <div class="modal-body">
                <p>如果您已收到订单，请确认收货。</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">确认</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
<?php endif;?>
<?php if ($model->status === Order::STATUS_UNPAID) :?>
<div class="modal fade" id="modal-cancel">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['/order/cancel', 'order' => $model->order_sn]) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">取消订单</h4>
            </div>
            <div class="modal-body">
                <p>您确定要取消该订单吗？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-danger">取消</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
<?php endif;?>
<?php if (in_array($model->status, [Order::STATUS_CANCELLED, Order::STATUS_COMPLETED])) :?>
<div class="modal fade" id="modal-del">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['/order/delete', 'order' => $model->order_sn]) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">删除订单</h4>
            </div>
            <div class="modal-body">
                <p>您确定要删除该订单吗？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-danger">删除</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
<?php endif;?>
<?php
if ($model->status === Order::STATUS_UNPAID) {
    $timeout = date('Y-m-d H:i:s', $model->timeout);
    $url = Url::to(['/order/timeout', 'order' => $model->order_sn]);
    $js = <<<JS
$(".countdown").countdown("{$timeout}", function (event) {
    $(this).text(event.strftime('%M:%S'));
}).on('finish.countdown', function(event) {
    $.ajax({
        url : '{$url}',
        type : 'post',
        success : function (data) {}
    });
});
JS;
    $this->registerJs($js);
}
