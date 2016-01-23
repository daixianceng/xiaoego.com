<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="password-reset">
    <p>您好 <?= $mobile ?></p>
    
    <p>您的验证码是【<?= $verifyCode ?>】</p>
    
    <p>感谢您在<?= Html::a(Yii::$app->name, Url::home(true)) ?>绑定邮箱。</p>
</div>
