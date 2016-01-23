<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use frontend\assets\CountdownAsset;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

CountdownAsset::register($this);

$this->title = '手机';
$this->params['breadcrumbs'][] = ['label' => '个人中心', 'url' => ['/i']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($step === '1') :?>
<div class="edit-form">
    <div class="callout callout-info">
        <p>当前手机号：<?= Yii::$app->user->identity->symbolMobile ?>，如需更换，请输入登录密码进行下一步。</p>
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
        'validateOnChange' => false,
        'validateOnBlur' => false,
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
        <div class="alert alert-success alert-msg-ok hidden" role="alert"></div>
        <div class="alert alert-danger alert-msg-err hidden" role="alert"></div>
	    <?= $form->field($changeMobileForm, 'mobile') ?>
        <?= $form->field($changeMobileForm, 'verifyCode', ['template' => "{label}\n{beginWrapper}\n<div class=\"row\">\n<div class=\"col-xs-6\">\n{input}\n</div>\n<div class=\"col-xs-6\">\n" . '<button type="button" class="btn btn-default btn-sendmsg2" data-mobile-target="#changemobileform-mobile" data-loading-text="验证码发送中..." autocomplete="off">发送验证码</button>' . "\n</div>\n</div>{hint}\n{error}\n{endWrapper}"]) ?>
		<div class="form-group">
            <div class="col-md-offset-2 col-md-8">
                <?= Html::submitButton('<i class="fa fa-upload"></i> 提交', ['class' => 'btn btn-warning', 'disabled' => true]) ?>
            </div>
        </div>
	<?php ActiveForm::end()?>
</div>
<?php
$urlSendMsg = Url::to(['/i/send-msg']);
$js = <<<JS
$('.btn-sendmsg2').click(function() {
    var target = $(this).attr('data-mobile-target');
    var mobile = $(target).val();
    var \$form = $(this.form);
    \$form.find('.alert-msg-err, .alert-msg-ok').addClass('hidden');
    $.ajax({
        url : '{$urlSendMsg}',
        type : 'post',
        data : {mobile : mobile},
        dataType : 'json',
        success : function (data) {
            if (data.status === 'ok') {
                \$form.find('.alert-msg-ok').html('验证码发送成功！<em>60</em> 秒后可再次发送！').removeClass('hidden');
                \$form.find('.btn-sendmsg2').prop('disabled', true);
                \$form.find('.alert-msg-ok em').countdown((new Date()).getTime() + 59000, function (event) {
                    $(this).text(event.strftime('%S'));
                }).on('finish.countdown', function(event) {
                    \$form.find('.alert-msg-ok').addClass('hidden');
                    \$form.find('.btn-sendmsg2').prop('disabled', false);
                });
            } else {
                \$form.find('.alert-msg-err').text(data.message).removeClass('hidden');
            }
        },
        error : function () {
            alert('验证码发送失败！');
        }
    });
});
JS;
$this->registerJs($js);
?>
<?php endif;?>
<?php
$script = <<<SCRIPT
$("form input").bind("change keypress", function() {\$(this.form).find("button").prop("disabled", false);$(this.form).find("input").unbind("change keypress")})
SCRIPT;
$this->registerJs($script);