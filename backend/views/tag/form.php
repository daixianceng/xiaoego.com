<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;
use kartik\select2\Select2;
use common\models\Store;

$this->title = $model->isNewRecord ? '添加标签' : '更新标签';
?>
<div class="row">
    <div class="col-lg-6">
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'store_id')->widget(Select2::className(), [
            'initValueText' => ($store = Store::findOne($model->store_id)) ? $store->name : '' ,
            'theme' => Select2::THEME_KRAJEE,
            'options' => ['placeholder' => '搜索店铺名称...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => [
                    'url' => Url::to(['/store/name-filter']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function (store) { return store.text; }'),
                'templateSelection' => new JsExpression('function (store) { return store.text; }'),
            ]
        ]) ?>
        <?= $form->field($model, 'name') ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>