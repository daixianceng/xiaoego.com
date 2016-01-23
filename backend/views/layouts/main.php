<?php
use yii\helpers\Html;
use yii\widgets\Menu;
use backend\widgets\Alert;
use backend\assets\AppAsset;

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
            <!-- /.dropdown -->
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><?= Html::a('<i class="fa fa-user fa-fw"></i> 我的资料', ['/member/update', 'id' => Yii::$app->user->id]) ?></li>
                    <li class="divider"></li>
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
                                    ['label' => '采购管理', 'url' => ['/apply'], 'active' => in_array($route, ['apply/index', 'apply/view'])]
                                ]
                            ],
                            [
                                'label' => '<i class="fa fa-shopping-cart fa-fw"></i> 商品管理<span class="fa arrow"></span>',
                                'url' => '#',
                                'items' => [
                                    ['label' => '商品列表', 'url' => ['/goods'], 'active' => in_array($route, ['goods/index', 'goods/add', 'goods/update', 'goods/img', 'goods/surplus'])],
                                    ['label' => '商品分类', 'url' => ['/category'], 'active' => in_array($route, ['category/index', 'category/add', 'category/update'])]
                                ]
                            ],
                            [
                                'label' => '<i class="fa fa-university fa-fw"></i> 学校管理<span class="fa arrow"></span>',
                                'url' => '#',
                                'items' => [
                                    ['label' => '学校列表', 'url' => ['/school'], 'active' => in_array($route, ['school/index', 'school/add', 'school/update'])],
                                    ['label' => '建筑列表', 'url' => ['/building'], 'active' => in_array($route, ['building/index', 'building/add', 'building/update'])],
                                ]
                            ],
                            [
                                'label' => '<i class="fa fa-map-marker fa-fw"></i> 营业点管理<span class="fa arrow"></span>',
                                'url' => '#',
                                'items' => [
                                    ['label' => '营业点列表', 'url' => ['/store'], 'active' => in_array($route, ['store/index', 'store/add', 'store/update', 'store/view'])],
                                    ['label' => '标签列表', 'url' => ['/tag'], 'active' => in_array($route, ['tag/index', 'tag/add', 'tag/update'])],
                                    ['label' => '用户列表', 'url' => ['/member'], 'active' => in_array($route, ['member/index', 'member/add', 'member/update'])]
                                ]
                            ],
                            [
                                'label' => '<i class="fa fa-user fa-fw"></i> 后台用户管理<span class="fa arrow"></span>',
                                'url' => '#',
                                'items' => [
                                    ['label' => '用户列表', 'url' => ['/admin'], 'active' => in_array($route, ['admin/index', 'admin/add', 'admin/update'])],
                                    ['label' => '我的资料', 'url' => ['/admin/profile'], 'active' => in_array($route, ['admin/profile'])],
                                ]
                            ],
                            [
                                'label' => '<i class="fa fa-users fa-fw"></i> 前台用户管理<span class="fa arrow"></span>',
                                'url' => '#',
                                'items' => [
                                    ['label' => '用户列表', 'url' => ['/user'], 'active' => in_array($route, ['user/index', 'user/view'])]
                                ]
                            ]
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

<?php $this->endContent() ?>