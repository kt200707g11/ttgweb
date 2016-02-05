<?php
use yii\helpers\Url;
?>
<header class="clearfix">
    <a href="#menu-header" id="menu-toggle" class="pull-left icon"></a>
    <a href="#settings-user" id="avatar-user" class="wrap-img"><img src="/images/default-avatar.jpg" alt=""></a>
    <div class="logo">
        <a href="/">metvuong</a>
    </div>
    <div id="menu-header" class="menu-header">
        <div class="wrap-menu">
            <a href="#" id="hide-menu" class="icon"></a>
            <ul class="clearfix">
                <li><a href="<?= Url::to(['/ad/index']) ?>">mua</a></li>
                <li><a href="#">thuê</a></li>
                <li><a href="<?= Url::to(['/ad/post']) ?>">bán / cho thuê</a></li>
                <li><a href="<?=Url::to(['building-project/index']);?>">dự án mới</a></li>
                <li class="line-city"><a href="#">tp.hcm</a></li>
                <li><a href="#" class="disable">hà nội</a></li>
                <li><a href="#"  ="disable">đà nẵng</a></li>
                <li><a href="#" class="disable">vũng tàu</a></li>
                <li><a href="#" class="disable">nha trang</a></li>
                <?php if(Yii::$app->user->isGuest){?>
                    <li class="regis-login">
                        <a href="<?=Url::to(['member/login'])?>">Đăng nhập</a>
                        <span>/</span>
                        <a href="<?=Url::to(['member/signup'])?>">Đăng ký</a>
                    </li>
                <?php }else{?>
                    <li class="regis-login">
                        <a href="<?=Url::to(['user-management/profile'])?>">
                            <span class="avatar-user"><img src="<?=Yii::$app->user->identity->profile->getAvatarUrl();?>" alt="" width="40" height="40"></span>
                            <span class="name-user"><?=Yii::$app->user->identity->profile->getDisplayName();?></span></a>
                    </li>
                    <li>
                        <a data-method="post" href="<?=\yii\helpers\Url::to(['/member/logout'])?>"><em class="icon-logout pull-right"></em><?=Yii::t('user', 'Logout')?></a>
                    </li>
                <?php }?>
            </ul>
        </div>
    </div>

    <div id="settings-user" class="settings-user">
        <a href="#" id="hide-settings" class="icon"></a>
        <ul class="clearfix">
            <li class="user-edit">
                <a href="#">
                    <span class="wrap-img"><img src="/images/default-avatar.jpg" alt="" width="40" height="40"></span>
                    <div>
                        <span class="name-user">Huan Ta</span>
                        <span class="address">Ho Chi Minh City, Vietnam</span>
                    </div>
                </a>
            </li>
            <li><a href="#">Đăng tin mới</a></li>
            <li><a href="#">Tất cả dự án</a></li>
            <li><a href="#">Statistics</a></li>
            <li><a href="#">Chat</a></li>
            <li><a href="#">Đăng xuất</a></li>
        </ul>
    </div>
</header>