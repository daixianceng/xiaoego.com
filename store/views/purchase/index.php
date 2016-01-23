<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\helpers\Url;
use common\models\Purchase;

$this->title = '采购管理';
?>
<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a>预购清单</a></li>
            <li role="presentation"><?= Html::a('采购历史', ['/apply']) ?></li>
        </ul>
        <div class="tab-content">
            <?php $form = ActiveForm::begin(); ?>
            <?php if (empty($createApplyForm->purchaseList)) :?>
            <div class="alert alert-warning">您的预购清单是空的。</div>
            <?php else :?>
            <?php foreach ($createApplyForm->purchaseList as $purchase) :?>
            <?php if ($purchase->isExpired) : ?>
            <div class="goods-item">
                <span class="goods-del" data-goodsId="<?= $purchase->goods_id ?>">删除</span>
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="col-sm-1"><?= Html::img(Url::toCover($purchase->goods->cover), ['class' => 'img-rounded']) ?></td>
                            <td class="col-sm-3"><?= Html::encode($purchase->goods->name) ?>（该商品已失效）</td>
                            <td class="col-sm-8">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php else : ?>
            <div class="goods-item">
                <span class="goods-del" data-goodsId="<?= $purchase->goods_id ?>">删除</span>
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="col-sm-1"><?= Html::img(Url::toCover($purchase->goods->cover), ['class' => 'img-rounded']) ?></td>
                            <td class="col-sm-3"><?= Html::encode($purchase->goods->name) ?></td>
                            <td class="col-sm-3"><?= Html::encode($purchase->goods->category->name) ?></td>
                            <td class="col-sm-2"><?= Html::textInput('count', $purchase->count, ['data-id' => $purchase->id]) ?> <?= Html::encode($purchase->goods->unit) ?></td>
                            <td class="col-sm-3 price">&yen; <?= $purchase->goods->price ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            <?php endforeach;?>
            <?= $form->field($createApplyForm, 'remark')->textarea(['placeholder' => '填写备注，如配送时间...', 'style' => 'resize:vertical;']) ?>
            <?php endif;?>
            <button type="submit" class="btn btn-lg btn-primary pull-right" onclick="return confirm('您确定要申请该预购清单吗？提交申请后不可修改。')"<?= empty($createApplyForm->purchaseList) ? ' disabled' : '' ?>>立即申请</button>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$urlCount = Url::to(['/purchase/count']);
$urlDelete = Url::to(['/purchase/delete']);
$js = <<<JS
$('input[name="count"]').change(function () {
    var \$this = $(this);
    var id = \$this.attr('data-id');
    var value = parseInt(\$this.val());
    if (isNaN(value) || value < 1) {
        value = 1;
    }
    if (value > 500) {
        value = 500;
    }
    \$this.val(value);
    $.ajax({
        url : '{$urlCount}?id=' + id,
        type : 'post',
        dataType : 'json',
        data : {value : value},
        success : function (data) {
            if (data.status !== 'ok') {
                alert(data.msg);
            }
        },
        error : function () {}
    });
});
$('.goods-del').click(function () {
    if (!confirm('要删除该数据吗？')) return false;
    var \$this = $(this);
    var id = $(this).attr('data-goodsId');
    $.ajax({
        url : '{$urlDelete}?id=' + id,
        type : 'post',
        dataType : 'json',
        success : function (data) {
            if (data.status === 'success') {
                \$this.parent().remove();
            } else {
                alert(data.data.message);
            }
        },
        error : function () {}
    });
});
JS;
$this->registerJs($js);