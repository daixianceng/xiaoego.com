<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Admin;
use kartik\select2\Select2;

$this->title = '我的资料';
?>
<div class="row">
    <div class="col-lg-6">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'options' => ['autocomplete' => 'off']
    ]); ?>
        <?= $form->field($model, 'username')->textInput(['autocomplete' => 'off']) ?>
        <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'off']) ?>
        <?= $form->field($model, 'email')->input('email') ?>
        <?= $form->field($model, 'real_name') ?>
        <?= $form->field($model, 'gender')->dropDownList(Admin::getGenderList()) ?>
        <?= $form->field($model, 'mobile') ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>