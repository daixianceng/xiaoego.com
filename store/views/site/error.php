<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>服务器处理您的请求时出现以上错误。</p>
    <p>请联系我们如果您觉得该错误很严重的话，谢谢。</p>

</div>
