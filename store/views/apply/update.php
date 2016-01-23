<?php
use yii\helpers\Html;
use common\helpers\Url;

$this->title = '采购编辑';
?>
<div class="row">
    <div class="col-lg-12">
        <?= Html::beginForm() ?>
        <?php foreach ($model->goods as $goods) :?>
        <div class="goods-item">
            <table class="table">
                <tbody>
                    <tr>
                        <td class="col-sm-1"><?= Html::img(Url::toCover($goods->cover), ['class' => 'img-rounded']) ?></td>
                        <td class="col-sm-3"><?= Html::encode($goods->name) ?></td>
                        <td class="col-sm-3"><?= Html::encode($goods->category) ?></td>
                        <td class="col-sm-2"><?= Html::textInput("count[]", $goods->count) ?> <?= Html::encode($goods->unit) ?></td>
                        <td class="col-sm-3 price">&yen; <?= $goods->price ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endforeach;?>
        <button type="submit" class="btn btn-lg btn-primary pull-right">提交修改</button>
        <?= Html::endForm() ?>
    </div>
</div>