<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\User */

$this->title = '基本信息';
$this->params['breadcrumbs'][] = ['label' => '个人中心', 'url' => ['/i']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="edit-form">
	<!-- edit personal details -->
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
        <div class="form-group">
            <label class="col-md-2 control-label"><?= $model->getAttributeLabel('mobile') ?></label>
            <div class="col-md-8">
                <p class="form-control-static"><?= $model->symbolMobile ?> <?= Html::a('<i class="fa fa-pencil"></i>', ['/i/mobile'], ['title' => '更换手机号']) ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label"><?= $model->getAttributeLabel('email') ?></label>
            <div class="col-md-8">
                <p class="form-control-static"><?= $model->email === null ? '（未设置）' : $model->email ?> <?= Html::a('<i class="fa fa-pencil"></i>', ['/i/email'], ['title' => '设置邮箱']) ?></p>
            </div>
        </div>
        <?= $form->field($model, 'nickname') ?>
        <?= $form->field($model, 'gender')->dropDownList(User::getGenderList()) ?>
		<div class="form-group">
            <div class="col-md-offset-2 col-md-8">
                <?= Html::submitButton('<i class="fa fa-save"></i> 保存', ['class' => 'btn btn-warning', 'disabled' => true]) ?>
            </div>
        </div>
	<?php ActiveForm::end()?>
</div>
<?php
$script = <<<SCRIPT
$("form input, form select").bind("change keypress", function() {\$(this.form).find("button").prop("disabled", false);$(this.form).find("input").unbind("change keypress")})
SCRIPT;
$this->registerJs($script);