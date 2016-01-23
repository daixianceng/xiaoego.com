<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Admin;
use common\models\Store;
use common\models\School;

$this->title = $model->isNewRecord ? '添加营业点用户' : '更新营业点用户';
?>
<div class="row">
    <div class="col-lg-6">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'options' => ['autocomplete' => 'off']
    ]); ?>
        <div class="form-group">
            <label class="control-label">学校</label>
            <?= Html::dropDownList('school', $model->isNewRecord ? '' : $model->store->school_id, School::getKeyValuePairs(), ['class' => 'form-control']) ?>
        </div>
        <?= $form->field($model, 'store_id')->dropDownList($model->isNewRecord ? [] : Store::getKeyValuePairs($model->store->school_id)) ?>
        <?= $form->field($model, 'username')->textInput(['autocomplete' => 'off']) ?>
        <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'off']) ?>
        <?= $form->field($model, 'email')->input('email') ?>
        <?= $form->field($model, 'real_name') ?>
        <?= $form->field($model, 'gender')->dropDownList(Admin::getGenderList()) ?>
        <?= $form->field($model, 'mobile') ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$url = Url::to(['/store/items']);
$js = <<<JS
$('[name="school"]').change(function () {
    var schoolId = $(this).val();
    $.ajax({
        url : '{$url}?id=' + schoolId,
        type : 'post',
        dataType : 'json',
        success : function (data) {
            if (data.status === 'ok') {
                $('#member-store_id').html(data.html);
            }
        },
        error : function () {}
    });
});
JS;
if ($model->isNewRecord) {
    $js .= '$(\'[name="school"]\').change()';
}
$this->registerJs($js);