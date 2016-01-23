<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = '密码';
$this->params['breadcrumbs'][] = ['label' => '个人中心', 'url' => ['/i']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($step === '1') :?>
<div class="edit-form">
    <div class="callout callout-info">
        <p>如需更改密码，请输入当前密码进行下一步。</p>
    </div>
	<?php $form = ActiveForm::begin([
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
        <?= $form->field($verifyPasswordForm, 'password')->passwordInput() ?>
		<div class="form-group">
            <div class="col-md-offset-2 col-md-8">
                <?= Html::submitButton('下一步 <i class="fa fa-angle-double-right"></i>', ['class' => 'btn btn-warning', 'disabled' => true]) ?>
            </div>
        </div>
	<?php ActiveForm::end()?>
</div>
<?php elseif ($step === '2') :?>
<div class="edit-form">
	<?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
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
	    <?= $form->field($changePasswordForm, 'password')->passwordInput() ?>
        <?= $form->field($changePasswordForm, 'passwordRepeat')->passwordInput() ?>
		<div class="form-group">
            <div class="col-md-offset-2 col-md-8">
                <?= Html::submitButton('<i class="fa fa-upload"></i> 提交', ['class' => 'btn btn-warning', 'disabled' => true]) ?>
            </div>
        </div>
	<?php ActiveForm::end()?>
</div>
<?php endif;?>
<?php
$script = <<<SCRIPT
$("form input").bind("change keypress", function() {\$(this.form).find("button").prop("disabled", false);$(this.form).find("input").unbind("change keypress")})
SCRIPT;
$this->registerJs($script);