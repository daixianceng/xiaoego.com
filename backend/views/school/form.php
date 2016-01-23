<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = $model->isNewRecord ? '添加学校' : '更新学校';
?>
<div class="row">
    <div class="col-lg-6">
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name') ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>