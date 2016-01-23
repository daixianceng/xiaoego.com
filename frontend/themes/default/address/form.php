<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Address;
use common\models\Building;
use common\models\School;

$this->title = $model->isNewRecord ? '添加收货地址' : '更新收货地址';
$this->params['breadcrumbs'][] = ['label' => '个人中心', 'url' => ['/i']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="edit-form">
<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-md-2',
            'wrapper' => 'col-md-8',
        ]
    ]
]); ?>
    <?= $form->field($model, 'consignee', [
            'template' => "{label}\n{beginWrapper}\n{beginRow}\n{beginCol}\n{input}\n{hint}\n{error}\n{endCol}\n{beginCol}\n{gender}\n{endCol}\n{endRow}\n{endWrapper}",
            'parts' => [
                '{gender}' => Html::activeRadioList($model, 'gender', Address::getGenderList()),
                '{beginRow}' => '<div class="row">',
                '{endRow}' => '</div>',
                '{beginCol}' => '<div class="col-xs-6">',
                '{endCol}' => '</div>'
            ]
    ]) ?>
    <?= $form->field($model, 'cellphone') ?>
    <?= $form->field($model, 'school_id')->dropDownList(School::getKeyValuePairs()) ?>
    <div class="form-group">
        <label class="col-md-2 control-label">收货地址</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-xs-6">
                    <div class="col-md-12"><?= $form->field($model, 'building_id', ['template' => "{input}\n{hint}\n{error}"])->dropDownList(Building::getKeyValuePairs($model->school_id)) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="col-md-12"><?= $form->field($model, 'room', ['template' => "{input}\n{hint}\n{error}"])->textInput(['placeholder' => '房间、地点等']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-8"><?= Html::submitButton('<i class="fa fa-save"></i> 保存', ['class' => 'btn btn-warning']) ?></div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<?php
$url = Url::to(['/address/buildings']);
$js = <<<JS
var stack = {};
$('#address-school_id').change(function () {
    var schoolId = this.value;
    if (stack[schoolId]) {
        $('#address-building_id').html(stack[schoolId]).change();
    }
    $.ajax({
        url : '{$url}' + '?id=' + schoolId,
        type : 'post',
        dataType : '',
        success : function (data) {
            $('#address-building_id').html(data.html).change();
            stack[schoolId] = data.html;
        },
        error : function () {}
    });
});
JS;
if ($model->isNewRecord) {
    $js .= '$("#address-school_id").change()';
}
$this->registerJs($js);