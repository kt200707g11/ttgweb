<?php
use yii\helpers\Url;
?>
<div class="pull-right wrap-menu-option">
<ul class="menu-home">
    <?php if(Yii::$app->user->isGuest){?>
        <li><a href="#" data-toggle="modal" data-target="#frmRegister"><em class="icon-user"></em>Đăng ký</a></li>
        <li><a href="#" data-toggle="modal" data-target="#frmLogin"><em class="icon-key"></em>Đăng nhập</a></li>
    <?php }else{?>
        <li><a href="<?=Url::to(['user-management/index'])?>">
                <em class="icon-user"></em>
                <?=!empty(Yii::$app->user->identity->profile->name) ? Yii::$app->user->identity->profile->name : Yii::$app->user->identity->email;?>
            </a>
        </li>
        <li>
            <a data-method="post" href="<?=\yii\helpers\Url::to(['/member/logout'])?>"><em class="icon-logout"></em><?=Yii::t('user', 'Logout')?></a>
        </li>
    <?php }?>
    <li class="lang-icon icon-en"><a href="<?=Url::current(['language-change'=>'en-US'])?>"></a></li>
    <li class="lang-icon icon-vi"><a href="<?=Url::current(['language-change'=>'vi-VN'])?>"></a></li>
</ul>
</div>