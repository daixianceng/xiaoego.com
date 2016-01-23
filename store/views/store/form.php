<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Store;

/* @var $this yii\web\View */
/* @var $model \common\models\Store */

$this->title = '我的店铺';
?>
<div class="row">
    <div class="col-lg-6">
    <?php if ($model->has_book || $model->has_down || $model->has_gift) :?>
    <div class="callout callout-info">
        <p><i class="fa fa-gift fa-fw"></i> 店铺特色：
            <?php if ($model->has_book) :?>
            <span class="label label-info">支持预定</span>
            <?php endif;?>
            <?php if ($model->has_gift) :?>
            <span class="label label-warning"><?= $model->giftMsg ?></span>
            <?php endif;?>
            <?php if ($model->has_down) :?>
            <span class="label label-danger"><?= $model->downMsg ?></span>
            <?php endif;?>
        </p>
    </div>
    <?php endif;?>
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'address') ?>
        <?= $form->field($model, 'cellphone') ?>
        <?= $form->field($model, 'telephone') ?>
        <?= $form->field($model, 'hours') ?>
        <?= $form->field($model, 'layout')->dropDownList(Store::getLayoutList()) ?>
        <?= $form->field($model, 'enable_sms')->checkbox() ?>
        <?= $form->field($model, 'auto_toggle', [
            'checkboxTemplate' => "<div class=\"checkbox form-inline\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{detail}\n{error}\n{hint}\n</div>",
            'parts' => [
                '{detail}' => '类型 ' . Html::activeDropDownList($model, 'toggle_type', Store::getToggleTypeList(), ['class' => 'form-control'])
            ]
        ])->checkbox() ?>
        <?= $form->field($model, 'notice') ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>