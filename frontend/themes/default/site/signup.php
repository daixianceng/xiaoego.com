<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use frontend\assets\CountdownAsset;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */
/* @var $step string */

if ($step === '2') {
    CountdownAsset::register($this);
}

$session = Yii::$app->session;
$session->open();

$this->title = '用户注册';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-md-6 col-md-offset-3 col-sm-offset-2">
        <?php if ($step !== '2') :?>
        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => false,
            'validateOnBlur' => true
        ]); ?>
            <?php if ($session->hasFlash('resignup')) :?>
            <div class="alert alert-warning" role="alert"><?= $session->getFlash('resignup') ?></div>
            <?php endif;?>
            <?= $form->field($model, 'mobile') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'captcha')->widget(Captcha::className(), [
                'template' => "<div class=\"row\">\n<div class=\"col-xs-6\">\n{input}\n</div>\n<div class=\"col-xs-4\">\n{image}\n</div>\n</div>"
            ])->hint('点击验证码换一个。') ?>
            <div class="form-group">
                <?= Html::submitButton('下一步 <i class="fa fa-angle-double-right"></i>', ['class' => 'btn btn-primary btn-fill']) ?>
            </div>
            <div class="form-group text-right">
                <?= Html::a('已有帐号？去登录<i class="fa fa-long-arrow-right"></i>', ['/site/login']) ?>
            </div>
        <?php ActiveForm::end(); ?>
        <?php else :?>
        <?php $form = ActiveForm::begin([
            'id' => 'form-verify',
            'enableAjaxValidation' => true,
            'validateOnBlur' => true,
        ]); ?>
            <div class="alert alert-success alert-msg-ok hidden" role="alert"></div>
            <div class="alert alert-danger alert-msg-err<?= $session->hasFlash('failedToSend') ? '' : ' hidden' ?>" role="alert"><?= $session->getFlash('failedToSend') ?></div>
            <div class="callout callout-info">
                <p>当前注册手机号：<?= $session['mobileSignup'] ?> <button type="button" class="btn btn-default btn-sendmsg" data-loading-text="验证码发送中..." autocomplete="off">再次发送</button></p>
            </div>
            <?= $form->field($signupMobileVerifyForm, 'verifyCode') ?>
            <div class="form-group">
                <?= Html::submitButton('完成注册', ['class' => 'btn btn-primary btn-fill']) ?>
            </div>
            <div class="form-group text-right">
                <?= Html::a('已有帐号？去登录<i class="fa fa-long-arrow-right"></i>', ['/site/login']) ?>
            </div>
        <?php ActiveForm::end(); ?>
        <?php
$urlSendMsg = Url::to(['/site/send-msg']);
$js = <<<JS
var sentSuccess = function () {
    var \$form = $('#form-verify');
    \$form.find('.alert-msg-ok').html('手机验证码发送成功，请注意接收！<em>60</em> 秒后可再次发送！').removeClass('hidden');
    \$form.find('.btn-sendmsg').prop('disabled', true);
    \$form.find('.alert-msg-ok em').countdown((new Date()).getTime() + 59000, function (event) {
        $(this).text(event.strftime('%S'));
    }).on('finish.countdown', function(event) {
        \$form.find('.alert-msg-ok').addClass('hidden');
        \$form.find('.btn-sendmsg').prop('disabled', false);
    });
}
$('.btn-sendmsg').click(function() {
    var \$form = $(this.form);
    \$form.find('.alert-msg-err, .alert-msg-ok').addClass('hidden');
    $.ajax({
        url : '{$urlSendMsg}',
        type : 'post',
        dataType : 'json',
        success : function (data) {
            if (data.status === 'ok') {
                sentSuccess();
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

if ($session->hasFlash('sentSuccess')) {
    $js .= 'sentSuccess();';
    $session->removeFlash('sentSuccess');
}

$this->registerJs($js);
?>
        <?php endif;?>
    </div>
</div>
