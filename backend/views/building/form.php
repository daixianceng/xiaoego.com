<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\School;

$this->title = $model->isNewRecord ? '添加学校建筑' : '更新学校建筑';
?>
<div class="row">
    <div class="col-lg-6">
    <?php if (!$model->isNewRecord) :?>
    <div class="callout callout-warning">
        <p>请不要更改<strong>学校</strong>，除非你确信在其它表中没有关联数据。</p>
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
        ]) */ ?>
        <?= $form->field($model, 'school_id')->dropDownList(School::getKeyValuePairs()) ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>