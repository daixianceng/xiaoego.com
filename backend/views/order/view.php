<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\helpers\Url;
use common\models\Order;
use backend\models\CancelOrderForm;

$this->title = '订单详情';
?>
<p>
    <?php if ($model->status === Order::STATUS_UNSHIPPED) :?>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-ship"><i class="fa fa-truck"></i> 配送</button>
    <?php if ($model->payment === Order::PAYMENT_OFFLINE) :?>
    <button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#modal-cancel"><i class="fa fa-times"></i> 取消</button>
    <?php endif;?>
    <?php endif;?>
</p>
<div class="row">
    <div class="col-lg-12">
        <div class="well well-lg">
            <h4><strong>订单编号</strong>【<?= $model->order_sn ?>】</h4>
            <p><strong>创建时间：</strong><?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
            <p><strong>状态：</strong><?= $model->statusMsg ?><?= $model->cancelled_msg ? '，取消原因：' . Html::encode($model->cancelled_msg) : '' ?></p>
            <p><strong>用户：</strong><?= Html::a($model->user->mobile, ['/user/view', 'id' => $model->user_id]) ?></p>
            <p><strong>购买店铺：</strong><?= Html::a($model->store->name, ['/store/view', 'id' => $model->store_id]) ?></p>
            <p><strong>收货地址：</strong><?= Html::encode($model->address) ?></p>
            <p><strong>送达时间：</strong><?= $model->bookTimeMsg ?></p>
            <p><strong>支付方式：</strong><?= $model->paymentMsg ?></p>
            <p><strong>商品总价：</strong>&yen; <?= $model->fee ?></p>
            <p><strong>商品总成本：</strong>&yen; <?= $model->cost ?></p>
            <p><strong>应付款：</strong>&yen; <?= $model->real_fee ?> <?= $model->preferentialPrettyMsg ?> <?= $model->newDownMsg ?></p>
            <p><strong>订单描述：</strong><?= Html::encode($model->description) ?></p>
            <?php if (!empty($model->remark)) :?>
            <p><strong>备注：</strong><?= Html::encode($model->remark) ?></p>
            <?php endif;?>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">商品详情</div>
            <table class="table table-middle table-hover" style="margin:0;">
                <tbody>
                    <?php foreach ($model->goods as $goods) :?>
                    <tr>
                        <td class="col-sm-1"><?= Html::img(Url::toCover($goods->cover), ['class' => 'img-rounded', 'width' => '40']) ?></td>
                        <td class="col-sm-3 text-center"><?= Html::encode($goods->name) ?></td>
                        <td class="col-sm-2 text-center"><?= Html::encode($goods->category) ?></td>
                        <td class="col-sm-2 text-center"><?= $goods->count ?> <?= Html::encode($goods->unit) ?></td>
                        <td class="col-sm-2 text-center price">&yen; <?= $goods->price ?></td>
                        <td class="col-sm-2 text-center price">&yen; <?= $goods->fee ?></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if ($model->status === Order::STATUS_UNSHIPPED) :?>
<div class="modal fade" id="modal-ship">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['/order/ship', 'id' => $model->id]) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">配送确认</h4>
            </div>
            <div class="modal-body">
                <p>将要配送该订单？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">确认</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
<?php if ($model->payment === Order::PAYMENT_OFFLINE) :?>
<div class="modal fade" id="modal-cancel">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'action' => ['/order/cancel', 'id' => $model->id],
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label' => 'col-md-2',
                        'wrapper' => 'col-md-8',
                        'hint' => ''
                    ]
                ]
            ]) ?>
            <?php $cancelOrderForm = new CancelOrderForm($model->id) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">取消订单确认</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($cancelOrderForm, 'msg')->textarea(['placeholder' => '请输入取消理由。', 'style' => 'resize:vertical;']) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-danger">确认</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php endif;?>
<?php endif;?>