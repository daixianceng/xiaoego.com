<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = '用户登录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-md-6 col-md-offset-3 col-sm-offset-2">
        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true
        ]); ?>
            <?= $form->field($model, 'mobile', ['selectors' => ['input' => '#loginform-mobile2']])->textInput(['id' => 'loginform-mobile2']) ?>
            <?= $form->field($model, 'password', ['selectors' => ['input' => '#loginform-password2']])->passwordInput(['id' => 'loginform-password2']) ?>
            <?= $form->field($model, 'rememberMe', ['selectors' => ['input' => '#loginform-rememberme2']])->checkbox(['id' => 'loginform-rememberme2']) ?>
            <div class="form-group">
                <?= Html::submitButton('登录', ['class' => 'btn btn-primary btn-fill']) ?>
            </div>
            <div class="form-group">
                <?= Html::a('忘记密码？', ['/site/request-password-reset']) ?>
                <?= Html::a('没有帐号？去注册<i class="fa fa-long-arrow-right"></i>', ['/site/signup'], ['class' => 'pull-right']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>