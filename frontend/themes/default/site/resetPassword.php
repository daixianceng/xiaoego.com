<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

$this->title = '密码重置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-md-6 col-md-offset-3 col-sm-offset-2">
        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'passwordRepeat')->passwordInput() ?>
            <div class="form-group">
                <?= Html::submitButton('确定', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>