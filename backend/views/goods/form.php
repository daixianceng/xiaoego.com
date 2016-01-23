<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\file\FileInput;
use common\models\Category;
use common\models\Goods;
use common\models\Store;
use common\models\School;
use common\helpers\Url;

$this->title = $model->isNewRecord ? '添加商品' : '更新商品';
?>
<div class="row">
    <div class="col-lg-6">
    <?php if (!$model->isNewRecord) :?>
    <div class="callout callout-warning">
        <p>请不要更改<strong>营业点</strong>与<strong>分类</strong>，除非你确信在其它表中没有关联数据。</p>
    </div>
    <?php endif;?>
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>
        <div class="form-group">
            <label class="control-label">学校</label>
            <?= Html::dropDownList('school', $model->isNewRecord ? '' : $model->store->school_id, School::getKeyValuePairs(), ['class' => 'form-control']) ?>
        </div>
        <?= $form->field($model, 'store_id')->dropDownList($model->isNewRecord ? [] : Store::getKeyValuePairs($model->store->school_id)) ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'category_id')->widget(Select2::className(), [
            'data' => Category::getKeyValuePairs(),
            'options' => ['placeholder' => '请选择分类'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>
        <?php if ($model->isNewRecord) :?>
        <?= $form->field($model, 'image')->widget(FileInput::className(), [
            'options' => ['accept' => 'image/*'],
            'pluginOptions' => [
                'showUpload' => false,
                'browseLabel' => '选择图片',
                'removeLabel' => '删除'
            ],
        ]) ?>
        <?php else :?>
        <?= $form->field($model, 'image')->widget(FileInput::className(), [
            'options' => ['accept' => 'image/*'],
            'pluginOptions' => [
                'showUpload' => false,
                'browseLabel' => '选择图片',
                'removeLabel' => '删除',
                'initialPreview' => Html::img(Url::toCover($model->cover), ['class' => 'file-preview-image'])
            ],
        ]) ?>
        <?php endif;?>
        <?= $form->field($model, 'description')->textarea() ?>
        <?= $form->field($model, 'price_original') ?>
        <?= $form->field($model, 'price') ?>
        <?= $form->field($model, 'cost') ?>
        <?php if ($model->isNewRecord) :?>
        <?= $form->field($model, 'surplus') ?>
        <?php else :?>
        <?= $form->field($model, 'surplus')->textInput(['disabled' => true]) ?>
        <?php endif;?>
        <?= $form->field($model, 'sales') ?>
        <?= $form->field($model, 'unit') ?>
        <?= $form->field($model, 'is_new')->dropDownList(Goods::getIsNewList()) ?>
        <?= $form->field($model, 'is_hot')->dropDownList(Goods::getIsHotList()) ?>
        <?= $form->field($model, 'is_promotion')->dropDownList(Goods::getIsPromotionList()) ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
            <?= $model->isNewRecord ? '' : Html::a('更新商品图片', ['/goods/img', 'id' => $model->id], ['class' => 'text-warning']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$urlStore = Url::to(['/store/items']);
$js = <<<JS
$('[name="school"]').change(function () {
    var schoolId = $(this).val();
    $.ajax({
        url : '{$urlStore}?id=' + schoolId,
        type : 'post',
        dataType : 'json',
        success : function (data) {
            if (data.status === 'ok') {
                $('#goods-store_id').html(data.html).change();
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