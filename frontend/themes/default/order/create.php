<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\helpers\Url;
use common\models\Address;
use common\models\Building;
use common\models\Order;
use common\models\Store;

/* @var $this yii\web\View */
/* @var $store Store */

$volume = Yii::$app->user->identity->getCartGoodsRealVolume($store->id);

$this->title = '订单确认';
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- Address modal starts -->
<div class="modal fade" id="modal-address" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $address = new Address();?>
            <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => true,
                'action' => ['/order/address-add'],
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
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4>收货地址</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($address, 'consignee', [
                        'template' => "{label}\n{beginWrapper}\n{beginRow}\n{beginCol}\n{input}\n{hint}\n{error}\n{endCol}\n{beginCol}\n{gender}\n{endCol}\n{endRow}\n{endWrapper}",
                        'parts' => [
                            '{gender}' => Html::activeRadioList($address, 'gender', Address::getGenderList()),
                            '{beginRow}' => '<div class="row">',
                            '{endRow}' => '</div>',
                            '{beginCol}' => '<div class="col-md-6">',
                            '{endCol}' => '</div>'
                        ]
                ]) ?>
                <?= $form->field($address, 'cellphone') ?>
                <div class="form-group">
                    <label class="col-md-2 control-label">收货地址</label>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="col-md-12"><?= $form->field($address, 'building_id', ['template' => "{input}\n{hint}\n{error}"])->dropDownList(Building::getKeyValuePairs($store->school_id)) ?></div>
                            </div>
                            <div class="col-xs-6">
                                <div class="col-md-12"><?= $form->field($address, 'room', ['template' => "{input}\n{hint}\n{error}"])->textInput(['placeholder' => '房间、地点等']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?= Html::activeHiddenInput($address, 'school_id', ['value' => $store->school_id]) ?>
                <?= Html::hiddenInput('storeId', $store->id) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
            <?php ActiveForm::end()?>
        </div>
    </div>
</div>
<!--/ Address modal ends -->
<?php $formOrder = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-8 col-md-9',
            'wrapper' => 'col-sm-4 col-md-3',
            'hint' => ''
        ]
    ]
]) ?>
<?php if ($store->status === Store::STATUS_REST) :?>
<div class="alert alert-warning" role="alert">对不起，该店铺休息中，暂不接受订单！</div>
<?php elseif ($store->has_least && $store->least_val > $volume) :?>
<div class="alert alert-warning" role="alert">对不起，该店铺<?= $store->least_val ?>元起送，请返回继续购物。</div>
<?php endif;?>
<?= Html::errorSummary($createOrderForm, ['class' => 'alert alert-danger']) ?>
<div class="address">
    <h2 class="br-orange default-head">选择收货地址</h2>
    <div class="row">
        <?php foreach ($addressList as $key => $address) :?>
        <div class="col-sm-4 col-md-3">
            <div class="address-item<?= $createOrderForm->addressId == $address->id ? ' address-active' : '' ?>">
                <h4><?= $address->consignee ?> （<?= $address->genderMsg ?>）<a href="<?= Url::to(['/order/address-edit', 'id' => $address->id]) ?>" data-addressId="<?= $address->id ?>" data-toggle="tooltip" data-placement="left" title="编辑"><i class="fa fa-pencil"></i></a></h4>
                <p><?= $address->cellphone ?></p>
                <p><?= $address->addressMsg ?></p>
                <div class="checked"></div>
                <?= Html::activeRadio($createOrderForm, 'addressId', ['value' => $address->id, 'label' => null, 'uncheck' => null]) ?>
            </div>
        </div>
        <?php endforeach;?>
        <div class="col-sm-4 col-md-3">
            <div class="address-item address-plus" title="添加新地址">
                <i class="fa fa-plus fa-4x"></i>
            </div>
        </div>
    </div>
</div>
<div class="ordergoods">
    <h2 class="br-red default-head">确认订单信息</h2>
    <?php if (count($cartGoodsList) > 0) :?>
    <?php foreach ($cartGoodsList as $cartGoods) :?>
        <?php if ($cartGoods->isExpired) :?>
        <div class="ordergoods-item">
            <span class="ordergoods-del" data-goodsId="<?= $cartGoods->goods_id ?>">删除</span>
            <table class="table expired" title="该商品已失效">
                <tbody>
                    <tr>
                        <td class="col-sm-1"><?= Html::img(Url::toCover($cartGoods->goods->cover), ['class' => 'img-rounded']) ?></td>
                        <td class="col-sm-3"><?= Html::encode($cartGoods->goods->name) ?>（已失效）</td>
                        <td class="col-sm-3"><?= Html::encode($cartGoods->goods->category->name) ?></td>
                        <td class="col-sm-1"><?= $cartGoods->count ?> <?= Html::encode($cartGoods->goods->unit) ?></td>
                        <td class="col-sm-2 price">&yen; <?= $price = $cartGoods->goods->price ?></td>
                        <td class="col-sm-2 price">&yen; <?= bcmul($price, $cartGoods->count, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php else :?>
        <div class="ordergoods-item">
            <table class="table">
                <tbody>
                    <tr>
                        <td class="col-sm-1"><?= Html::img(Url::toCover($cartGoods->goods->cover), ['class' => 'img-responsive img-rounded']) ?></td>
                        <td class="col-sm-3"><?= Html::encode($cartGoods->goods->name) ?></td>
                        <td class="col-sm-3"><?= Html::encode($cartGoods->goods->category->name) ?></td>
                        <td class="col-sm-1"><?= $cartGoods->count ?> <?= Html::encode($cartGoods->goods->unit) ?></td>
                        <td class="col-sm-2 price">&yen; <?= $price = $cartGoods->goods->price ?></td>
                        <td class="col-sm-2 price">&yen; <?= bcmul($price, $cartGoods->count, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif;?>
    <?php endforeach;?>
    <?php else :?>
    <div class="alert alert-warning" role="alert">您当前购物车为空！</div>
    <?php endif;?>
</div>
<div class="payment">
    <h2 class="br-blue default-head">选择支付方式</h2>
    <div class="row">
        <div class="col-md-2 col-sm-3 col-xs-5">
            <div class="payment-item<?= $createOrderForm->payment === Order::PAYMENT_ONLINE ? ' payment-active' : '' ?>">
                <h4>在线支付</h4>
                <div class="checked"></div>
                <?= Html::activeRadio($createOrderForm, 'payment', ['value' => Order::PAYMENT_ONLINE, 'label' => null, 'uncheck' => null]) ?>
            </div>
        </div>
        <div class="col-md-2 col-sm-3 col-xs-5">
            <div class="payment-item<?= $createOrderForm->payment === Order::PAYMENT_OFFLINE ? ' payment-active' : '' ?>">
                <h4>货到付款</h4>
                <div class="checked"></div>
                <?= Html::activeRadio($createOrderForm, 'payment', ['value' => Order::PAYMENT_OFFLINE, 'label' => null, 'uncheck' => null]) ?>
            </div>
        </div>
    </div>
</div>
<div class="place">
    <div>
        <?= $formOrder->field($createOrderForm, 'remark', [
            'template' => "{beginWrapper}\n{input}\n{hint}\n{endWrapper}",
            'horizontalCssClasses' => [
                'wrapper' => 'col-md-12',
                'hint' => ''
            ]
        ])->textarea(['placeholder' => '添加备注，如商品口味、颜色、您的位置等信息。', 'style' => 'resize:vertical;']) ?>
        <?php if (Yii::$app->params['enableNewDown'] && $volume >= Yii::$app->params['newDownUpper'] && Yii::$app->user->identity->has_new_down) :?>
        <?= $formOrder->field($createOrderForm, 'newDown')->dropDownList(['1' => Yii::$app->params['newDownMsg'], '0' => '不使用优惠']) ?>
        <?php endif;?>
        <?php if ($createOrderForm->getPreferentialItems()) :?>
        <?= $formOrder->field($createOrderForm, 'preferential')->dropDownList($createOrderForm->getPreferentialItems()) ?>
        <?php endif;?>
        <?php if ($store->has_book) :?>
        <?= $formOrder->field($createOrderForm, 'bookTime')->dropDownList($createOrderForm->getBookTimeItems()) ?>
        <?php endif;?>
    </div>
    <div class="place-total">
        实付款：<span class="price"><i class="fa fa-cny"></i> <span><?= $volume ?></span></span>
    </div>
    <div class="place-btn">
        <?= Html::a('返回继续购物', Url::to(['/store/index', 'id' => $store->id]), ['class' => 'text-warning']) ?>
        <button class="btn btn-danger btn-lg" type="submit"<?= (count($cartGoodsList) == 0 || $store->status === Store::STATUS_REST || ($store->has_least && $store->least_val > $volume)) ? ' disabled' : '' ?>>立即下单</button>
    </div>
</div>
<?php ActiveForm::end() ?>
<?php
$urlAdd = Url::to(['/order/address-add']);
$urlLoad = Url::to(['/order/address-load']);
$urlRealFee = Url::to(['/order/real-fee']);
$urlDelete = Url::to(['/cart/delete']);
$js = <<<JS
$('.address-item').click(function () {
    if ($(this).find('input[type="radio"]').length > 0) {
        $(this).find('input[type="radio"]').prop("checked", true);
        $('.address-item').removeClass('address-active');
        $(this).addClass('address-active');
    }
});
$('.address-plus').click(function () {
    $('#modal-address form').attr('action', '{$urlAdd}')[0].reset();
    $('#modal-address').modal('show');
});
$('.address-item h4 a').click(function () {
    var addressId = $(this).attr('data-addressId');
    $('#modal-address form').attr('action', $(this).attr('href'));
    $.ajax({
        url : '{$urlLoad}' + '?id=' + addressId,
        type : 'post',
        dataType : 'json',
        success : function (data) {
            $('#address-consignee').val(data.consignee).change();
            $('#address-gender input[name="Address[gender]"][value="' + data.gender + '"]').prop('checked', true).change();
            $('#address-cellphone').val(data.cellphone).change();
            $('#address-building_id').val(data.building_id).change();
            $('#address-room').val(data.room).change();
            $('#modal-address').modal('show');
        },
        error : function () {}
    });
    return false;
});

$('.payment-item').click(function () {
    if ($(this).find('input[type="radio"]').length > 0) {
        $(this).find('input[type="radio"]').prop("checked", true);
        $('.payment-item').removeClass('payment-active');
        $(this).addClass('payment-active');
    }
});

$('#createorderform-preferential, #createorderform-newdown').change(function (e) {
    var payment = $('input[name="CreateOrderForm[payment]"]').val();
    var newdown = $('#createorderform-newdown').val();
    var preferential = $('#createorderform-preferential').val();
    
    newdown = newdown == undefined ? 0 : newdown;
    preferential = preferential == undefined ? 0 : preferential;
    
    $.ajax({
        url : '{$urlRealFee}',
        type : 'post',
        dataType : 'json',
        data : {payment : payment, preferential : preferential, newdown : newdown, storeId : {$store->id}},
        success : function (data) {
            if (data.status === 'ok') {
                $('.place-total .price span').text(data.realFee);
            } else {
                window.location.reload();
            }
        },
        error : function () {}
    });
});
$('#createorderform-preferential, #createorderform-newdown').change();

$('.ordergoods-del').click(function () {
    var goodsId = parseInt($(this).attr('data-goodsId'));
    var \$item = $(this).parent();
    $.ajax({
        url : '{$urlDelete}',
        type : 'post',
        dataType : 'json',
        data : {goodsId : goodsId},
        success : function (data) {
            if (data.status === 'ok') {
                \$item.remove();
            }
        },
        error : function () {}
    });
});
JS;
$this->registerJs($js);