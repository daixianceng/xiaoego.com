<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\helpers\Url;
use common\models\Apply;
use backend\models\RejectApplyForm;

$this->title = '采购详情';
?>
<p>
    <?php if ($model->status === Apply::STATUS_PENDING) :?>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-pass"><i class="fa fa-check"></i> 通过</button>
    <button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#modal-reject"><i class="fa fa-times"></i> 驳回</button>
    <?php endif;?>
</p>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <p class="col-md-6"><strong>申请单号：</strong> 【<?= Html::encode($model->apply_sn) ?>】（<?= $model->statusMsg ?>）</p>
                    <p class="col-md-6 text-right hidden-xs"><strong>申请时间：</strong><?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
                </div>
                <div class="row">
                    <p class="col-md-12"><strong>学校：</strong><?= Html::encode($model->store->school->name) ?></p>
                </div>
                <div class="row">
                    <p class="col-md-12"><strong>营业点：</strong><?= Html::encode($model->store->name) ?></p>
                </div>
                <div class="row">
                    <p class="col-md-12"><strong>手机：</strong><?= Html::encode($model->store->cellphone) ?></p>
                </div>
                <?php if ($model->remark) : ?>
                <div class="row">
                    <p class="col-md-12"><strong>备注：</strong><?= Html::encode($model->remark) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <table class="table table-middle table-hover" style="margin:0;">
                <tbody>
                    <?php foreach ($model->goods as $goods) :?>
                    <tr>
                        <td class="col-sm-1"><?= Html::img(Url::toCover($goods->cover), ['class' => 'img-rounded', 'width' => '40']) ?></td>
                        <td class="col-sm-3 text-center"><?= Html::a(Html::encode($goods->name), ['/goods/surplus', 'id' => $goods->goods_id], ['title' => '查看库存变化记录']) ?></td>
                        <td class="col-sm-2 text-center"><?= Html::encode($goods->category) ?></td>
                        <td class="col-sm-2 text-center"><?= $goods->count ?> <?= Html::encode($goods->unit) ?></td>
                        <td class="col-sm-2 text-center price">&yen; <?= $goods->price ?></td>
                        <td class="col-sm-2 text-center price">&yen; <?= $goods->fee ?></td>
                    </tr>
                    <?php endforeach;?>
                    <tr>
                        <td class="col-sm-10 text-center" colspan="5"></td>
                        <td class="col-sm-2 text-center price">&yen; <?= $model->fee ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4 class="page-header">申请日志：</h4>
        <table class="table table-striped table-hover table-bordered">
            <tbody>
                <?php foreach ($model->logs as $log) :?>
                <tr>
                    <td class="col-sm-4"><?= Yii::$app->formatter->asDatetime($log->created_at, "php:Y-m-d H:i:s") ?></td>
                    <td class="col-sm-8"><?= Html::encode($log->remark) ?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
<?php if ($model->status === Apply::STATUS_PENDING) :?>
<div class="modal fade" id="modal-pass">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['/apply/pass', 'id' => $model->id]) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">通过确认</h4>
            </div>
            <div class="modal-body">
                <p>要通过该申请吗？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">通过</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-reject">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'action' => ['/apply/reject', 'id' => $model->id],
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
            <?php $rejectApplyForm = new RejectApplyForm($model->id);?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">驳回确认</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($rejectApplyForm, 'remark')->textarea(['placeholder' => '请输入驳回理由。', 'style' => 'resize:vertical;']) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-danger">驳回</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php endif;?>