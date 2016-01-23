<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@app/views/layouts/main.php');
?>
<div class="inner-banner">
	<!-- page title / heading -->
	<h2><?= Html::encode($this->title) ?></h2>
	<!-- breadcrumb -->
	<?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []
    ]) ?>
</div>

<!-- main content -->
<div class="main-content">
    <div class="container">
        <div class="inner-content">
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>
</div>
<?php $this->endContent(); ?>