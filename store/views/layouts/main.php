<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu;
use store\widgets\Alert;
use store\assets\AppAsset;
use common\models\Store;
use common\models\Order;

AppAsset::register($this);

$route = Yii::$app->requestedAction->uniqueId;
?>
<?php $this->beginContent('@app/views/layouts/base.php'); ?>
<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0;">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <?= Html::a(Yii::$app->name, Yii::$app->homeUrl, ['class' => 'navbar-brand']) ?>
        </div>
        <!-- /.navbar-header -->

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><?= Html::a('<i class="fa fa-user fa-fw"></i> 我的资料', ['/member/profile']) ?></li>
                    <li class="divider"></li>
                    <?php if (Yii::$app->user->identity->store->status === Store::STATUS_ACTIVE) :?>
                    <li><?= Html::a('<i class="fa fa-coffee fa-fw"></i> 休息', ['/store/rest']) ?></li>
                    <li class="divider"></li>
                    <?php elseif (Yii::$app->user->identity->store->status === Store::STATUS_REST) :?>
                    <li><?= Html::a('<i class="fa fa-check fa-fw"></i> 营业', ['/store/active']) ?></li>
                    <li class="divider"></li>
                    <?php endif;?>
                    <li>
                        <?= Html::beginForm(['/site/logout']) ?>
                        <?= Html::submitButton('<i class="fa fa-sign-out fa-fw"></i> 退出') ?>
                        <?= Html::endForm() ?>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse collapse in" aria-expanded="true">
                <?= Menu::widget([
                        'encodeLabels' => false,
                        'submenuTemplate' => "\n<ul class=\"nav nav-second-level collapse\">\n{items}\n</ul>\n",
                        'options' => [
                            'class' => 'nav',
                            'id' => 'side-menu'
                        ],
                        'items' => [
                            ['label' => '<i class="fa fa-dashboard fa-fw"></i> 仪表盘', 'url' => ['/site/index']],
                            [
                                'label' => '<i class="fa fa-home fa-fw"></i> 笑e购<span class="fa arrow"></span>',
                                'url' => '#',
                                'items' => [
                                    ['label' => '订单管理', 'url' => ['/order'], 'active' => in_array($route, ['order/index', 'order/view'])],
                                    ['label' => '商品管理', 'url' => ['/goods'], 'active' => in_array($route, ['goods/index', 'goods/update', 'goods/surplus'])],
                                    ['label' => '采购管理', 'url' => ['/purchase'], 'active' => in_array($route, ['purchase/index', 'apply/index', 'apply/update', 'apply/view'])]
                                ]
                            ],
                            [
                                'label' => '<i class="fa fa-cog fa-fw"></i> 设置<span class="fa arrow"></span>',
                                'url' => '#',
                                'items' => [
                                    ['label' => '我的店铺', 'url' => ['/store/update'], 'active' => in_array($route, ['store/update'])],
                                    ['label' => '店铺标签', 'url' => ['/tag'], 'active' => in_array($route, ['tag/index', 'tag/add', 'tag/update'])],
                                    ['label' => '我的资料', 'url' => ['/member/profile'], 'active' => in_array($route, ['member/index', 'member/profile'])],
                                ]
                            ],
                        ]
                ]) ?>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?= Alert::widget() ?>
            </div>
        </div>
        <?= $content ?>
    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<audio class="audio-order hidden" controls="controls" data-count="<?= Order::getCountByStoreId(Yii::$app->user->identity->store_id) ?>">
    <source src="<?= Url::base() ?>/audio/alert.ogg" type="audio/ogg">
    <source src="<?= Url::base() ?>/audio/alert.mp3" type="audio/mpeg">
</audio>
<?php $this->endContent() ?>