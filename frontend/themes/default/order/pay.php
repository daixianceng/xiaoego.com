<?php
use yii\helpers\Url;
use yii\helpers\Html;
use frontend\models\PayOrderForm;
use frontend\assets\CountdownAsset;

/* @var $this \yii\web\View */
/* @var $order \common\models\Order */

CountdownAsset::register($this);

$this->title = '订单支付';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modal fade" id="modal-helper">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">支付结果确认</h4>
            </div>
            <div class="modal-body">
                <p>您已成功支付订单了吗？</p>
            </div>
            <div class="modal-footer">
                <?= Html::a('支付遇到问题', ['/site/help'], ['class' => 'btn btn-danger']) ?>
                <?= Html::a('支付成功', ['/order/detail', 'order' => $order->order_sn], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-to-offline">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['/order/to-offline', 'order' => $order->order_sn]) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">转换确认</h4>
            </div>
            <div class="modal-body">
                <p>您确定要转为货到付款方式吗？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-danger">确定</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
<div class="alert alert-info">
该订单商品属于实时商品，请您在<span class="countdown"></span>内完成支付。若您想使用货到付款，仍然可以 <a href="" data-toggle="modal" data-target="#modal-to-offline" style="text-decoration: underline;">一键到付</a>。
</div>
<div class="orderview">
    <h4>订单号：<?= $order->order_sn ?><span class="hidden-xs"><?= Yii::$app->formatter->asDatetime($order->created_at) ?></span></h4>
    <div class="orderview-body">
        <p>购买店铺：<?= Html::encode($order->store->name) ?></p>
        <p>应付款：&yen; <?= $order->real_fee ?> <?= $order->preferentialPrettyMsg ?>  <?= $order->newDownMsg ?></p>
        <p>订单描述：<?= $order->description ?></p>
        <?php if (!empty($order->remark)) :?>
        <p>备注：<?= Html::encode($order->remark) ?></p>
        <?php endif;?>
    </div>
</div>
<div class="platform">
    <h2 class="br-orange default-head">选择第三方支付平台</h2>
    <?= Html::beginForm('', 'post', ['target' => '_blank']) ?>
    <div class="row">
        <?php if (strpos(Yii::$app->request->userAgent, 'MicroMessenger') !== false) :?>
        <div class="col-sm-4 col-md-3">
            <div class="platform-item">
                <a href="<?= Url::to(['/payment/wxpay', 'order' => $order->order_sn]) ?>"><?= Html::img($this->theme->getUrl('images/payment/wxpay.png')) ?></a>
            </div>
        </div>
        <?php endif; ?>
        <div class="col-sm-4 col-md-3">
            <div class="platform-item platform-submit">
                <a href="#"><?= Html::img($this->theme->getUrl('images/payment/alipay.png')) ?></a>
                <?= Html::activeRadio($model, 'platform', ['value' => PayOrderForm::PLATFORM_ALIPAY, 'label' => null, 'uncheck' => null]) ?>
            </div>
        </div>
    </div>
    <?= Html::endForm() ?>
</div>
<?php
$timeout = date('Y-m-d H:i:s', $order->timeout);
$url = Url::to(['/order/timeout', 'order' => $order->order_sn]);
$js = <<<JS
$('.platform-submit').click(function () {
    $(this).find('input[type="radio"]').prop("checked", true);
    $(this).parents('form').submit();
    $('#modal-helper').modal('show');
    return false;
});
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