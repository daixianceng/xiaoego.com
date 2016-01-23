<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Address;

/* @var $model Address */
?>
<div class="myaddress-item">
    <h4 class="br-red">收货人：<?= $model->consignee ?>（<?= $model->genderMsg ?>）
        <span><?= $model->is_default ? '（默认）' : Html::a('[设为默认]', ['/address/default', 'id' => $model->id]) ?></span>
    </h4>
    <div class="myaddress-body">
        <a class="myaddress-link" href="<?= Url::to(['/address/update', 'id' => $model->id]) ?>">
            <p><?= Html::encode($model->cellphone) ?></p>
            <p><?= Html::encode($model->addressMsg) ?></p>
        </a>
        <div class="myaddress-go">
            <?= Html::a('<i class="fa fa-pencil"></i>', ['/address/update', 'id' => $model->id]) ?>
            <?= Html::a('<i class="fa fa-trash"></i>', ['/address/delete', 'id' => $model->id], ['onclick' => 'return confirm("您要删除该地址吗？");']) ?>
        </div>
    </div>
</div>