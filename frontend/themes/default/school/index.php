<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Store;

/* @var $this yii\web\View */
/* @var $model common\models\School */

$this->title = $model->name;
?>
<?php if ($model->getStores()->count() > 0) :?>
<h2 class="store-head">所有店铺</h2>
<div class="store">
    <div class="row">
        <?php foreach ($model->stores as $store) :?>
        <div class="col-xs-6 col-md-4 col-lg-3">
            <div class="store-item store-blue-l" data-unique="<?= $store->id ?>">
                <a href="<?= Url::to(['/store/index', 'id' => $store->id]) ?>">
                    <div class="store-block">
                        <h3><?= Html::encode($store->name) ?></h3>
                        <?php if ($store->status === Store::STATUS_REST) :?>
                        <div class="store-ribbon">休息中</div>
                        <?php endif;?>
                        <p><i class="fa fa-tree fa-fw"></i> <?= Html::encode($store->address) ?></p>
                    </div>
                </a>
            </div>
        </div>
        <?php endforeach;?>
    </div>
</div>
<?php else :?>
<div class="alert alert-warning" role="alert">该学校暂时没有开通营业点，给您带来不便，敬请您的谅解！</div>
<?php endif;?>