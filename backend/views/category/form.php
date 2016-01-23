<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Admin;
use kartik\select2\Select2;
use common\models\Category;

$this->title = $model->isNewRecord ? '添加分类' : '更新分类';
?>
<div class="row">
    <div class="col-lg-6">
    <?php if (!$model->isNewRecord) :?>
    <div class="callout callout-warning">
        <p>请不要更改<strong>分类</strong>，谢谢。</p>
    </div>
    <?php endif;?>
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'slug') ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>