<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Goods;

$this->title = $model->isNewRecord ? '添加商品' : '更新商品';
?>
<div class="row">
    <div class="col-lg-6">
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'description')->textarea() ?>
        <?= $form->field($model, 'is_new')->dropDownList(Goods::getIsNewList()) ?>
        <?= $form->field($model, 'is_hot')->dropDownList(Goods::getIsHotList()) ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>