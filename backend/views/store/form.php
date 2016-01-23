<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\School;
use common\models\Store;

$this->title = $model->isNewRecord ? '添加营业点' : '更新营业点';
?>
<div class="row">
    <div class="col-lg-6">
    <?php if (!$model->isNewRecord) :?>
    <div class="callout callout-warning">
        <p>请不要更改<strong>学校</strong>与<strong>类型</strong>，除非你确信在其它表中没有关联数据。</p>
    </div>
    <?php endif;?>
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name') ?>
        <?php /* $form->field($model, 'school_id')->widget(Select2::className(), [
            'initValueText' => ($school = School::findOne($model->school_id)) ? $school->name : '' ,
            'theme' => Select2::THEME_KRAJEE,
            'options' => ['placeholder' => '搜索学校名称...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => [
                    'url' => Url::to(['/school/name-filter']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function (store) { return store.text; }'),
                'templateSelection' => new JsExpression('function (store) { return store.text; }'),
            ]
        ])*/ ?>
        <?= $form->field($model, 'school_id')->dropDownList(School::getKeyValuePairs()) ?>
        <?= $form->field($model, 'address') ?>
        <?= $form->field($model, 'cellphone') ?>
        <?= $form->field($model, 'telephone') ?>
        <?= $form->field($model, 'hours') ?>
        <?= $form->field($model, 'enable_sms')->checkbox() ?>
        <?= $form->field($model, 'auto_toggle', [
            'checkboxTemplate' => "<div class=\"checkbox form-inline\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{detail}\n{error}\n{hint}\n</div>",
            'parts' => [
                '{detail}' => '类型 ' . Html::activeDropDownList($model, 'toggle_type', Store::getToggleTypeList(), ['class' => 'form-control'])
            ]
        ])->checkbox() ?>
        <?= $form->field($model, 'has_book')->checkbox() ?>
        <?= $form->field($model, 'has_least', [
            'checkboxTemplate' => "<div class=\"checkbox form-inline\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{detail}\n{error}\n{hint}\n</div>",
            'parts' => [
                '{detail}' => '起送价 ' . Html::activeTextInput($model, 'least_val', ['class' => 'form-control']) . ' 元'
            ]
        ])->checkbox() ?>
        <?= $form->field($model, 'has_down', [
            'checkboxTemplate' => "<div class=\"checkbox form-inline\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{detail}\n{error}\n{hint}\n</div>",
            'parts' => [
                '{detail}' => '满 ' . Html::activeTextInput($model, 'down_upper', ['class' => 'form-control']) . ' 元减 ' . Html::activeTextInput($model, 'down_val', ['class' => 'form-control'])
            ]
        ])->checkbox() ?>
        <?= $form->field($model, 'has_gift', [
            'checkboxTemplate' => "<div class=\"checkbox form-inline\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{detail}\n{error}\n{hint}\n</div>",
            'parts' => [
                '{detail}' => '满 ' . Html::activeTextInput($model, 'gift_upper', ['class' => 'form-control']) . ' 元送 ' . Html::activeTextInput($model, 'gift_val', ['class' => 'form-control'])
            ]
        ])->checkbox() ?>
        <?= $form->field($model, 'notice') ?>
        <?= $form->field($model, 'layout')->dropDownList(Store::getLayoutList()) ?>
        <?= $form->field($model, 'status')->dropDownList(Store::getStatusList()) ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>