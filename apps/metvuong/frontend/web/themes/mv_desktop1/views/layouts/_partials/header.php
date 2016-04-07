<?php
use yii\helpers\Url;
use vsoft\ad\models\AdProduct;
use frontend\models\AdProductSearch;
?>
<header class="clearfix header">
    <div class="container wrap-header">
        <div class="m-header">
            <a href="#menu-header" id="menu-toggle" class="pull-left"><span class="icon"></span></a>
            <a href="#settings-user" id="avatar-user" class="wrap-img wrapNotifyTotal">
                <div>
                    <?php if(Yii::$app->user->isGuest){?>
                        <img src="/images/default-avatar.jpg" alt="">
                    <?php } else{?>
                        <img id="headAvatar" src="<?=Yii::$app->user->identity->profile->getAvatarUrl();?>" alt="">
                        <?php if(!empty($this->params['notify_total'])){?>
                            <span id="notifyTotal"><?=$this->params['notify_total'];?></span>
                        <?php }?>
                    <?php }?>
                </div>
            </a>
            <div class="logo">
                <a href="<?=Url::home()?>">metvuong</a>
            </div>
            <div id="menu-header" class="menu-header">
                <div class="wrap-menu">
                    <a href="#" id="hide-menu" class="icon"></a>
                    <ul class="clearfix">
                        <li class="<?=!empty($this->params['menuBuy']) ? 'active' : '' ;?>"><a href="<?= Url::to(['/ad/index', 'type' => AdProduct::TYPE_FOR_SELL, 'city_id' => AdProductSearch::DEFAULT_CITY, 'district_id' => AdProductSearch::DEFAULT_DISTRICT]) ?>"><div><span class="icon icon-search"></span></div><?=Yii::t('general', 'Buy')?></a></li>
                        <li class="<?=!empty($this->params['menuRent']) ? 'active' : '' ;?>"><a href="<?= Url::to(['/ad/index', 'type' => AdProduct::TYPE_FOR_RENT, 'city_id' => AdProductSearch::DEFAULT_CITY, 'district_id' => AdProductSearch::DEFAULT_DISTRICT]) ?>"><div><span class="icon icon-search"></span></div><?=Yii::t('general', 'Rent')?></a></li>
                        <li class="<?=!empty($this->params['menuSell']) ? 'active' : '' ;?>"><a href="<?= Url::to(['/ad/post']) ?>"><div><span class="icon icon-key"></span></div><?=Yii::t('general', 'Sell')?></a></li>
                        <li class="<?=!empty($this->params['menuProject']) ? 'active' : '' ;?>"><a href="<?=Url::to(['building-project/index']);?>"><div><span class="icon icon-home"></span></div><?=Yii::t('general', 'New Project')?></a></li>
                        <li class="<?=!empty($this->params['menuNews']) ? 'active' : '' ;?>"><a href="<?=Url::to(['news/index']);?>"><div><span class="icon icon-news"></span></div><?=Yii::t('general', 'News')?></a></li>
                        <li class="<?=!empty($this->params['menuPricing']) ? 'active' : '' ;?>"><a href="<?=Url::to(['/payment/package'])?>"><div><span class="icon icon-tags"></span></div><?=Yii::t('general', 'Pricing')?></a></li>
                    </ul>
                </div>
            </div>

            <div id="settings-user" class="settings-user">
                <a href="#" id="hide-settings" class="icon"></a>
                <?php if(Yii::$app->user->isGuest){?>
                    <ul class="clearfix">
                        <li><a href="<?=Url::to(['member/login'])?>" class="user-login-link" ><?=Yii::t('user', 'Sign In')?></a></li>
                        <li><a href="<?=Url::to(['member/signup'])?>" class="user-signup-link"><?=Yii::t('user', 'Sign Up')?></a></li>
                        <li class="flag-lang">
                            <p class="pull-right">
                                <a href="<?=Url::current(['language-change'=>'en-US'])?>"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/flag-en.png' ?>" alt=""></a>
                                <a href="<?=Url::current(['language-change'=>'vi-VN'])?>"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/flag-vn.png' ?>" alt=""></a>
                            </p>
                            <?=Yii::t('general', 'Language')?>
                        </li>
                    </ul>
                <?php } else{?>
                <ul class="clearfix">
                    <li class="user-edit">
                        <a href="<?=Url::to(['member/profile', 'username'=>Yii::$app->user->identity->username])?>">
                            <span class="wrap-img"><img src="<?=Yii::$app->user->identity->profile->getAvatarUrl();?>" alt="" width="40" height="40"></span>
                            <div>
                                <p><span class="name-user"><?=Yii::$app->user->identity->profile->getDisplayName();?></span>
                                <span class="address"><?=empty(Yii::$app->user->identity->location) ? "" : Yii::$app->user->identity->location->city?></span></p>
                            </div>
                        </a>
                    </li>
                    <!-- <li><a href="<?= Url::to(['/ad/post']) ?>"><em class="icon-plus"></em>Đăng tin mới</a></li> -->
                    <li><a href="<?=Url::to(['/notification/index', 'username'=> Yii::$app->user->identity->username])?>" class="wrapNotifyOther"><div><span class="icon icon-alert"></span></div><?=Yii::t('activity', 'Notification')?>
                            <?php if(!empty($this->params['notify_other'])){?>
                                <span id="notifyOther" class="notifi"><?=$this->params['notify_other'];?></span>
                            <?php }?>
                        </a></li>
                    <li><a href="<?=Url::to(['/dashboard/ad', 'username'=> Yii::$app->user->identity->username])?>"><div><span class="icon icon-listings"></span></div><?=Yii::t('ad', 'Listings')?></a></li>
                    <li><a href="<?=Url::to(['/chat/index', 'username'=> Yii::$app->user->identity->username])?>" class="wrapNotifyChat"><div><span class="icon icon-chat"></span></div><?=Yii::t('chat', 'Chat')?>
                            <?php if(!empty($this->params['notify_chat'])){?>
                                <span id="notifyChat" class="notifi"><?=$this->params['notify_chat'];?></span>
                            <?php }?>
                    </a></li>
                    <li><a data-method="post" href="<?=Url::to(['member/update-profile', 'username'=>Yii::$app->user->identity->username])?>"><div><span class="icon icon-settings"></span></div><?=Yii::t('user', 'Setting')?></a></li>
                    <li><a data-method="post" href="<?=Url::to(['/member/logout'])?>"><div><span class="icon icon-logout"></span></div><?=Yii::t('user', 'Log Out')?></a></li>
                    <li class="flag-lang">
                        <p class="pull-right">
                            <a href="<?=Url::current(['language-change'=>'en-US'])?>"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/flag-en.png' ?>" alt=""></a>
                            <a href="<?=Url::current(['language-change'=>'vi-VN'])?>"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/flag-vn.png' ?>" alt=""></a>
                        </p>
                        <?=Yii::t('general', 'Language')?>
                    </li>
                </ul>
                <?php } ?>
            </div>
        </div>

        <div class="dt-header clearfix">
            <div class="user-login pull-right">
                <?php if(Yii::$app->user->isGuest){?>
                <div class="box-dropdown guest-dropdown">
                    <a href="#" class="icon-guest val-selected wrap-img">
                        <div><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/default-avatar.jpg' ?>" alt=""></div>
                        Guest
                    </a>
                    <div class="item-dropdown hide-dropdown">
                        <ul class="clearfix">
                            <li><a href="<?=Url::to(['/member/login'])?>" class="user-login-link"><?=Yii::t('user', 'Sign In')?></a></li>
                            <li><a href="<?=Url::to(['/member/signup'])?>" class="user-signup-link"><?=Yii::t('user', 'Sign Up')?></a></li>
                            <li class="flag-lang">
                                <div class="pull-right">
                                    <a href="<?=Url::current(['language-change'=>'en-US'])?>"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/flag-en.png' ?>" alt=""></a>
                                    <a href="<?=Url::current(['language-change'=>'vi-VN'])?>"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/flag-vn.png' ?>" alt=""></a>
                                </div>
                                <?=Yii::t('general', 'Language')?>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php } else{?>
                    <ul class="pull-left list-redire">
                        <li>
                            <a class="tooltip-show wrapNotifyChat" href="<?=Url::to(['/chat/index', 'username'=> Yii::$app->user->identity->username])?>" data-toggle="tooltip" data-placement="bottom" title="<?=Yii::t('chat', 'Chat')?>">
                                <span class="wrap-icon-svg">
                                    <svg class="icon-svg icon-chat-svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-chat-svg"></use></svg>
                                </span>
                                <?php if(!empty($this->params['notify_chat'])){?>
                                    <span id="notifyChat" class="notifi"><?=$this->params['notify_chat'];?></span>
                                <?php }?>
                            </a>
                        </li>
                        <li>
                            <a class="tooltip-show wrapNotifyOther" href="<?=Url::to(['/notification/index', 'username'=> Yii::$app->user->identity->username])?>" data-toggle="tooltip" data-placement="bottom" title="<?=Yii::t('activity', 'Notification')?>">
                                <span class="wrap-icon-svg">
                                    <svg class="icon-svg icon-bell-svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-bell-svg"></use></svg>
                                </span>
                                <?php if(!empty($this->params['notify_other'])){?>
                                    <span id="notifyOther" class="notifi"><?=$this->params['notify_other'];?></span>
                                <?php }?>
                            </a>
                        </li>
                        <li>
                            <a class="tooltip-show" href="<?=Url::to(['/dashboard/ad', 'username'=> Yii::$app->user->identity->username])?>" data-toggle="tooltip" data-placement="bottom" title="Dashboard">
                                <span class="wrap-icon-svg">
                                    <svg class="icon-svg icon-dasboar-svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-dasboar-svg"></use></svg>
                                </span>
                            </a>
                        </li>
                    </ul>
                    <div class="user-edit box-dropdown">
                        <a class="val-selected wrapNotifyTotal tooltip-show" data-toggle="tooltip" data-placement="bottom" href="#" title="<?=Yii::t('user', 'Profile')?>">
                            <span class="wrap-img"><img src="<?=Yii::$app->user->identity->profile->getAvatarUrl();?>" alt="" width="40" height="40"></span>
                            <div>
                                <p><span class="name-user"><?=Yii::$app->user->identity->profile->getDisplayName();?></span>
                                    <span class="address"><?=empty(Yii::$app->user->identity->location) ? "" : Yii::$app->user->identity->location->city?></span></p>
                            </div>
                        </a>
                        <div class="item-dropdown hide-dropdown">
                            <ul class="clearfix">
                                <li>
                                    <a href="<?=Url::to(['member/profile', 'username'=>Yii::$app->user->identity->username])?>">
                                        <div>
                                            <span class="icon icon-per"></span>
                                        </div>
                                        <?=Yii::t('user', 'Profile')?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?=Url::to(['/notification/index', 'username'=> Yii::$app->user->identity->username])?>" class="wrapNotifyOther">
                                        <div><span class="icon icon-alert"></span>
                                        <?php if(!empty($this->params['notify_other'])){?>
                                            <span id="notifyOther" class="notifi"><?=$this->params['notify_other'];?></span>
                                        <?php }?>
                                        </div><?=Yii::t('activity', 'Notification')?>
                                    </a>
                                </li>
                                <li><a href="<?=Url::to(['/dashboard/ad', 'username'=> Yii::$app->user->identity->username])?>"><div><span class="icon icon-listings"></span></div><?=Yii::t('ad', 'Listings')?></a></li>
                                <li><a href="<?=Url::to(['/chat/index', 'username'=> Yii::$app->user->identity->username])?>" class="wrapNotifyChat">
                                        <div>
                                            <span class="icon icon-chat"></span>
                                            <?php if(!empty($this->params['notify_chat'])){?>
                                                <span id="notifyChat" class="notifi"><?=$this->params['notify_chat'];?></span>
                                            <?php }?>
                                        </div><?=Yii::t('chat', 'Chat')?>
                                    </a></li>
                                <li><a data-method="post" href="<?=Url::to(['member/update-profile', 'username'=>Yii::$app->user->identity->username])?>"><div><span class="icon icon-settings"></span></div><?=Yii::t('user', 'Setting')?></a></li>
                                <li><a data-method="post" href="<?=Url::to(['/member/logout'])?>"><div><span class="icon icon-logout"></span></div><?=Yii::t('user', 'Log Out')?></a></li>
                                <li class="flag-lang">
                                    <div class="pull-right">
                                        <a href="<?=Url::current(['language-change'=>'en-US'])?>"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/flag-en.png' ?>" alt=""></a>
                                        <a href="<?=Url::current(['language-change'=>'vi-VN'])?>"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/flag-vn.png' ?>" alt=""></a>
                                    </div>
                                    <?=Yii::t('general', 'Language')?>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <ul class="clearfix list-menu">
                <li class="dt-logo"><a href="/" class="wrap-img"><img src="<?= Yii::$app->view->theme->baseUrl . '/resources/images/logo.png' ?>" alt=""></a></li>
                <li class="<?=!empty($this->params['menuBuy']) ? 'active' : '' ;?>"><a href="<?= Url::to(['/ad/index', 'type' => AdProduct::TYPE_FOR_SELL, 'city_id' => AdProductSearch::DEFAULT_CITY, 'district_id' => AdProductSearch::DEFAULT_DISTRICT]) ?>"><?=Yii::t('general', 'Buy')?></a></li>
                <li class="<?=!empty($this->params['menuRent']) ? 'active' : '' ;?>"><a href="<?= Url::to(['/ad/index', 'type' => AdProduct::TYPE_FOR_RENT, 'city_id' => AdProductSearch::DEFAULT_CITY, 'district_id' => AdProductSearch::DEFAULT_DISTRICT]) ?>"><?=Yii::t('general', 'Rent')?></a></li>
                <li class="<?=!empty($this->params['menuSell']) ? 'active' : '' ;?>"><a href="<?= Url::to(['/ad/post']) ?>"><?=Yii::t('general', 'Sell')?></a></li>
                <li class="<?=!empty($this->params['menuProject']) ? 'active' : '' ;?>"><a href="<?=Url::to(['building-project/index']);?>"><?=Yii::t('general', 'New Project')?></a></li>
                <li class="<?=!empty($this->params['menuNews']) ? 'active' : '' ;?>"><a href="<?=Url::to(['news/index']);?>"><?=Yii::t('general', 'News')?></a></li>
                <li class="<?=!empty($this->params['menuPricing']) ? 'active' : '' ;?>"><a href="<?=Url::to(['/payment/package'])?>"><?=Yii::t('general', 'Pricing')?></a></li>
            </ul>
        </div>
    </div>
</header>
<div id="popup-login" class="modal fade popup-common" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" class="btn-close close" data-dismiss="modal" aria-label="Close"><span class="icon icon-close"></span></a>
                <div class="wrap-popup">
                    <div class="inner-popup">
                        <div class="wrap-body-popup">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="popup-signup" class="modal fade popup-common" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" class="btn-close close" data-dismiss="modal" aria-label="Close"><span class="icon icon-close"></span></a>
                <div class="wrap-popup">
                    <div class="inner-popup">
                        <div class="wrap-body-popup">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(document).on('click', '.user-login-link', function (e) {
            if(checkMobile()){
                return true;
            }
            e.preventDefault();
            $('body').loading();
            $.ajax({
                type: "get",
                url: "<?=Url::to(['/member/login'])?>",
                success: function (data) {
                    $('body').loading({done: true});
                    $('#popup-login .wrap-body-popup').html(data);
                    $('#popup-login').modal('show');
                }
            });

        });
        $(document).on('click', '.user-signup-link', function (e) {
            if(checkMobile()){
                return true;
            }
            e.preventDefault();
            $('body').loading();
            $.ajax({
                type: "get",
                url: "<?=Url::to(['/member/signup'])?>",
                success: function (data) {
                    $('body').loading({done: true});
                    $('#popup-signup .wrap-body-popup').html(data);
                    $('#popup-signup').modal('show');
                }
            });
        });

        $('.user-login .box-dropdown, .guest-dropdown').dropdown({
            styleShow: 0,
            selectedValue: false
        });

    });
</script>