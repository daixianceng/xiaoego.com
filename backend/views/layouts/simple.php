<?php
use yii\helpers\Html;
use backend\widgets\Alert;
?>
<?php $this->beginContent('@app/views/layouts/base.php'); ?>
<div class="container">

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
    <div class="row">
        <div class="col-lg-12">
            <?= $content ?>
        </div>
    </div>

</div>
<!-- /#wrapper -->
<?php $this->endContent() ?>