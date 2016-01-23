<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\file\FileInput;
use common\models\Goods;
use common\helpers\Url;

$preview = [];
foreach ($model->images as $img) {
    $preview[] = Html::img(Url::toGoods($img->name), ['class' => 'file-preview-image']);
}

$this->title = '更新商品图片';
?>
<div class="row">
    <div class="col-lg-6">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
        <?= $form->field($model, 'name')->textInput(['disabled' => true]) ?>
        <?= $form->field($model, 'photos[]')->widget(FileInput::className(), [
            'options' => ['accept' => 'image/*', 'multiple' => true],
            'pluginOptions' => [
                'showUpload' => false,
                'browseLabel' => '选择照片',
                'removeLabel' => '删除',
                'initialPreview' => $preview
            ],
        ]) ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> 保存', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('返回商品详情', ['/goods/update', 'id' => $model->id], ['class' => 'text-warning']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>