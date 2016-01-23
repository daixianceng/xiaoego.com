<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = '找回密码';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-md-6 col-md-offset-3 col-sm-offset-2">
        <div class="reset-password">
            <ul class="nav nav-tabs">
                <li role="presentation"<?= $type === 'sms' ? ' class="active"' : '' ?>><?= Html::a('短信找回', ['/site/request-password-reset', 'type' => 'sms']) ?></li>
                <li role="presentation"<?= $type === 'email' ? ' class="active"' : '' ?>><?= Html::a('邮箱找回', ['/site/request-password-reset', 'type' => 'email']) ?></li>
            </ul>
            <div class="tab-content">
                <?php if ($type === 'sms') :?>
                    <?php if ($step !== '2') :?>
                        <?php if (Yii::$app->session->hasFlash('resetAgain')) :?>
                        <div class="alert alert-danger" role="alert"><?= Yii::$app->session->getFlash('resetAgain') ?></div>
                        <?php endif;?>
                        <?php if (Yii::$app->session->hasFlash('smsFailure')) :?>
                        <div class="alert alert-danger" role="alert"><?= Yii::$app->session->getFlash('smsFailure') ?></div>
                        <?php else :?>
                        <div class="alert alert-info" role="alert">请输入您所注册的手机号。</div>
                        <?php endif;?>
                        <?php $form = ActiveForm::begin(); ?>
                            <?= $form->field($model, 'mobile') ?>
                            <?= $form->field($model, 'captcha')->widget(Captcha::className(), [
                                'captchaAction' => 'site/password-reset-captcha',
                                'template' => "<div class=\"row\">\n<div class=\"col-xs-6\">\n{input}\n</div>\n<div class=\"col-xs-4\">\n{image}\n</div>\n</div>"
                            ])->hint('点击验证码换一个。') ?>
                            <div class="form-group">
                                <?= Html::submitButton('下一步 <i class="fa fa-angle-double-right"></i>', ['class' => 'btn btn-primary']) ?>
                            </div>
                        <?php ActiveForm::end(); ?>
                    <?php else :?>
                        <?php if (Yii::$app->session->hasFlash('resetErr')) :?>
                        <div class="alert alert-danger" role="alert"><?= Yii::$app->session->getFlash('resetErr') ?></div>
                        <?php else :?>
                        <div class="alert alert-info" role="alert">请输入您所收到的短信验证码。</div>
                        <?php endif;?>
                        <?php $form = ActiveForm::begin([
                            'enableAjaxValidation' => true,
                        ]); ?>
                            <?= $form->field($model, 'verifyCode') ?>
                            <div class="form-group">
                                <?= Html::submitButton('下一步 <i class="fa fa-angle-double-right"></i>', ['class' => 'btn btn-primary']) ?>
                            </div>
                        <?php ActiveForm::end(); ?>
                    <?php endif;?>
                <?php else :?>
                    <?php if (Yii::$app->session->hasFlash('emailSent')) :?>
                    <div class="alert alert-success" role="alert"><?= Yii::$app->session->getFlash('emailSent') ?></div>
                    <?php elseif (Yii::$app->session->hasFlash('emailFailure')) :?>
                    <div class="alert alert-danger" role="alert"><?= Yii::$app->session->getFlash('emailFailure') ?></div>
                    <?php else :?>
                    <div class="alert alert-info" role="alert">请输入您所绑定的邮箱。</div>
                    <?php endif;?>
                    <?php $form = ActiveForm::begin(); ?>
                        <?= $form->field($model, 'email') ?>
                        <?= $form->field($model, 'captcha')->widget(Captcha::className(), [
                            'captchaAction' => 'site/password-reset-captcha',
                            'template' => "<div class=\"row\">\n<div class=\"col-xs-6\">\n{input}\n</div>\n<div class=\"col-xs-4\">\n{image}\n</div>\n</div>"
                        ])->hint('点击验证码换一个。') ?>
                        <div class="form-group">
                            <?= Html::submitButton('<i class="fa fa-send"></i> 发送', ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>