<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = '加入我们';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content">
    <!-- heading -->
    <h2 class="br-orange default-head">工程师</h2>
    <div class="page-section">
        <?= Html::img($this->theme->getUrl('images/engineer.jpg'), ['class' => 'img-thumbnail img-left']) ?>
        <h4>Bootstrap设计师</h4>
        <p>正如你所见，笑e购是用<a href="http://getbootstrap.com/" target="_blank">Bootstrap</a>设计的，如果你把自己定位成一个普通的美工，或者没有接触过Bootstrap，你应该看一下其他职位。如果你很擅长使用Bootstrap设计完美的响应式界面，当然你对HTML5、CSS以及jQuery也应该相当熟悉，那么你就是我们极力挖取的人才。</p>
        <div class="clearfix"></div>
    </div>
    <!-- heading -->
    <h2 class="br-orange default-head">校园加盟</h2>
    <div class="page-section">
        <?= Html::img($this->theme->getUrl('images/manager.jpg'), ['class' => 'img-thumbnail img-left']) ?>
        <h4>校园经理</h4>
        <p>校园经理是连接营业点与笑e购总部的枢纽，负责笑e购品牌的宣传，以及统计学校内各营业点的商品数量，及时补货。</p>
        <div class="clearfix"></div>
    </div>
    <div class="page-section">
        <?= Html::img($this->theme->getUrl('images/delivery.jpg'), ['class' => 'img-thumbnail img-left']) ?>
        <h4>营业点店长</h4>
        <p>如果你是一个学生，不想在宿舍内浪费光阴，那么证明你实力的机会来了！加入笑e购店长，只需要在同一栋楼内送货即可。</p>
        <div class="clearfix"></div>
    </div>
</div>
