<?php
/**
 * Created by PhpStorm.
 * User: Nhut Tran
 * Date: 10/1/2015 9:09 AM
 *
 * Template 1 columns 8 rows
 */
use yii\bootstrap\Html;

?>
<div class="siderbar widget-dqt clearfix siderbar-style">
    <div class="widget-title clearfix"><h2>đáng quan tâm</h2></div>
    <?php if (!empty($news)) { ?>
    <ul>
        <?php foreach ($news as $k => $n) { ?>
            <li>
                <a class="pull-left wrap-img" href="<?=\yii\helpers\Url::to(['news/view', 'id' => $n->id, 'slug' => $n->slug])?>">
                    <img src="/store/news/show/<?= $n->banner ?>" alt="<?= $n->title ?>" style="width: 82px; height: 55px;">
                </a>
                <div>
                    <?= Html::a(strlen($n->title) > 30 ? mb_substr($n->title, 0, 30) . '...' : $n->title, ['view', 'id' => $n->id, 'slug' => $n->slug], ['class' => 'color-title-link']) ?>
                    <p> <?= strlen($n->brief) > 100 ? mb_substr($n->brief, 0, 100) : $n->brief ?> </p>
                </div>
            </li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>
