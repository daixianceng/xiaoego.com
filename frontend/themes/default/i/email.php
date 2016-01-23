<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = '邮箱';
$this->params['breadcrumbs'][] = ['label' => '个人中心', 'url' => ['/i']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($step === '1') :?>
<div class="edit-form">
    <?php if (empty(Yii::$app->user->identity->email)) :?>
    <?= Alert::widget([
        'options' => ['class' => 'alert-warning'],
        'body' => '绑定邮箱有助于您找回密码。'
    ]) ?>
    <?php else :?>
    <div class="callout callout-info">
        <p>当前绑定邮箱：<?= Yii::$app->user->identity->email ?> <button type="button" class="btn btn-link" data-toggle="modal" data-target="#modal-remove-email">解除绑定</button></p>
    </div>
    <div class="modal fade" id="modal-remove-email" tabindex="-1" role="dialog" aria-labelledby="modal-remove-email" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">解除绑定</h4>
                </div>
                <div class="modal-body">
                    <p>确定要将邮箱解除绑定吗？</p>
                </div>
                <div class="modal-footer">
                    <?= Html::beginForm(['/i/remove-email']) ?>
                    <?= Html::button('关闭', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
                    <?= Html::submitButton('解除绑定', ['class' => 'btn btn-danger']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
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
        <?= $form->field($emailBindRequestForm, 'email')->input('email') ?>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8"><?= Html::submitButton('下一步 <i class="fa fa-angle-double-right"></i>', ['class' => 'btn btn-warning', 'disabled' => true]) ?></div>
        </div>
    <?php ActiveForm::end()?>
</div>
<?php else :?>
<div class="edit-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'enableAjaxValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-md-2',
                'wrapper' => 'col-md-8',
                'hint' => ''
            ]
        ]
    ]) ?>
        <?= $form->field($bindEmailForm, 'verifyCode', ['template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n" . '<button type="button" id="email-send" class="btn btn-default" data-loading-text="验证码发送中..." autocomplete="off">再次发送验证码</button> <em></em>' . "\n{endWrapper}"]) ?>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8">
                <?= Html::submitButton('<i class="fa fa-paperclip"></i> 绑定', ['class' => 'btn btn-warning', 'disabled' => true]) ?>
            </div>
        </div>
    <?php ActiveForm::end()?>
</div>
<?php endif;?>
<?php
$sendEmailUrl = Url::to(['/i/send-email']);
$script = <<<SCRIPT
$('#email-send').bind('click', function () {
    var \$btn = $(this).button('loading');
    $.ajax({
        url : '{$sendEmailUrl}',
        type : 'post',
        dataType : 'json',
        success : function(data, textStatus, jqXHR) {
            if (data.status === 'ok') {
                \$btn.next('em').text('验证码发送成功，60秒后可以再次发送！');
            } else {
                \$btn.next('em').text(data.msg);
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            alert(textStatus);
        },
        complete : function(XMLHttpRequest, textStatus) {
            \$btn.button('reset');
        }
    })
});
$("form input").bind("change keypress", function() {\$(this.form).find("button").prop("disabled", false);$(this.form).find("input").unbind("change keypress")})
SCRIPT;
$this->registerJs($script);