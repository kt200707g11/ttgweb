<?php
use yii\helpers\Url;

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key=AIzaSyASTv_J_7DuXskr5SaCZ_7RVEw7oBKiHi4', ['depends' => ['yii\web\YiiAsset'], 'async' => true, 'defer' => true]);
//$this->registerJsFile(Yii::$app->view->theme->baseUrl . '/resources/js/detail.js', ['position' => View::POS_END]);
$this->registerCss('.map-wrap {position: relative;} .map-wrap:after {display: block; content: ""; padding-top: 75%;} .map-inside {position: absolute; width: 100%; height: 100%;} #map {height: 100%;}');

Yii::$app->view->registerMetaTag([
    'name' => 'keywords',
    'content' => $model->location
]);
Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => $model->description
]);

Yii::$app->view->registerMetaTag([
    'property' => 'og:title',
    'content' => $model->location
]);
Yii::$app->view->registerMetaTag([
    'property' => 'og:description',
    'content' => $model->description
]);
Yii::$app->view->registerMetaTag([
    'property' => 'og:type',
    'content' => 'article'
]);
Yii::$app->view->registerMetaTag([
    'property' => 'og:image',
    'content' => $model->logoUrl
]);

$lbl_updating = Yii::t('general', 'Updating');

$fb_appId = '680097282132293'; // stage.metvuong.com
if(strpos(Yii::$app->urlManager->hostInfo, 'dev.metvuong.com'))
    $fb_appId = '736950189771012';
else if(strpos(Yii::$app->urlManager->hostInfo, 'local.metvuong.com'))
    $fb_appId = '891967050918314';
?>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : <?=$fb_appId?>,
            xfbml      : true,
            version    : 'v2.5'
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/vi_VN/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<div class="title-fixed-wrap">
    <div class="container">
        <div class="detail-duan-moi">
            <!-- <div class="title-top"><?= $model->name?></div> -->
            <div class="wrap-duan-moi row">
                <div class="col-xs-12 col-md-9 col-left">
                    <div class="gallery-detail swiper-container">
                        <div class="swiper-wrapper">
                            <?php
                            if(!empty($model->gallery)) {
                                $gallery = explode(',', $model->gallery);
                                if (count($gallery) > 0) {
                                    foreach ($gallery as $image) {
                                        ?>
                                        <div class="swiper-slide">
                                            <div class="img-show">
                                                <div>
                                                    <img src="<?= \yii\helpers\Url::to('/store/building-project-images/' . $image) ?>"
                                                        alt="<?= $model->location ?>">
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                }
                            } else {
                                ?>
                                <div class="swiper-slide">
                                    <div class="img-show">
                                        <div>
                                            <img src="<?=$model->logoUrl?>" alt="<?=$model->location?>">
                                        </div>
                                    </div>
                                </div>
                            <?php }  ?>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"><span></span></div>
                        <div class="swiper-button-prev"><span></span></div>
                    </div>
                    <div class="item infor-address-duan">
                        <p><?= $model->investment_type ?></p>
                        <strong><?= $model->name?></strong>
                        <?= empty($model->location) ? $lbl_updating : $model->location ?>
                        <ul class="pull-right icons-detail">
                            <li><a href="#popup-share-social" class="icon icon-share-td"></a></li>
        <!--                    <li><a href="#" class="icon save-item" data-id="4115" data-url="/ad/favorite"></a></li>-->
                            <li><a href="#popup-map" class="icon icon-map-loca"></a></li>
                        </ul>
                    </div>
                    <div class="item infor-time">
                        <p><strong><?=Yii::t('project','Investor')?>: </strong> <?= empty($model->investors[0]->name) ? $lbl_updating : $model->investors[0]->name ?></p>
                        <p><strong><?=Yii::t('project', 'Start date')?>: </strong> <?=empty($model->start_date) ? $lbl_updating : date('d/m/Y', $model->start_date) ?></p>
                        <p><strong><?=Yii::t('project', 'Finish time')?>:</strong> <?=empty($model->estimate_finished) ? $lbl_updating : $model->estimate_finished ?></p>
                    </div>
                    <div class="item detail-infor">
                        <p class="title-attr-duan"><?=Yii::t('ad', 'Description')?></p>
                        <p><?=$model->description ?></p>
                    </div>
                    <div class="item infor-attr">
                        <p class="title-attr-duan"><?=Yii::t('project', 'Project information')?></p>
                        <ul class="clearfix">
                            <li><strong><?=Yii::t('project', 'Facade width')?>:</strong><?=!empty($model->facade_width) ? $model->facade_width : $lbl_updating?></li>
                            <li><strong><?=Yii::t('project', 'Floor')?>:</strong><?=!empty($model->floor_no) ? $model->floor_no : $lbl_updating?></li>
                            <li><strong><?=Yii::t('project', 'Lift')?>:</strong><?=!empty($model->lift) ? $model->lift : $lbl_updating?></li>
                        </ul>
                    </div>
                    <div class="item tien-ich-duan">
                        <p class="title-attr-duan"><?=Yii::t('project', 'Facility')?></p>
                        <?php
                        $facilityListId = explode(",", $model->facilities);
                        $facilities = \vsoft\ad\models\AdFacility::find()->where(['id' => $facilityListId])->all();
                        $count_facilities = count($facilities);
                        if($count_facilities > 0){
                        ?>
                        <ul class="clearfix">
                            <?php foreach($facilities as $facility){ ?>
                            <li>
                                <div><p><span class="icon-ti icon-sport"></span><?= $facility->name ?></p></div>
                            </li>
                            <?php } ?>
                        </ul>
                        <?php } else {?>
                        <p><?=$lbl_updating;?></p>
                        <?php }?>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3 col-right sidebar-col">
                    <div class="item-sidebar">
                        <div class="title-sidebar">DỰ ÁN NỔI BẬT</div>
                        <ul class="clearfix list-post">
                            <li>
                                <div class="wrap-item-post">
                                    <a href="#" class="rippler rippler-default">
                                        <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg"></div></div>
                                    </a>
                                    <p class="infor-by-up">Căn hộ chung cư Bán</p>
                                    <p class="name-post"><a href="#">LOREM IPSUM DOLORIT </a></p>
                                    <p class="fs-15 font-400">21 Nguyễn Trung Ngạn, P. Bến Nghé, Q1</p>
                                </div>
                            </li>
                            <li>
                                <div class="wrap-item-post">
                                    <a href="#" class="rippler rippler-default">
                                        <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg"></div></div>
                                    </a>
                                    <p class="infor-by-up">Căn hộ chung cư Bán</p>
                                    <p class="name-post"><a href="#">LOREM IPSUM DOLORIT </a></p>
                                    <p class="fs-15 font-400">21 Nguyễn Trung Ngạn, P. Bến Nghé, Q1</p>
                                </div>
                            </li>
                            <li>
                                <div class="wrap-item-post">
                                    <a href="#" class="rippler rippler-default">
                                        <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg"></div></div>
                                    </a>
                                    <p class="infor-by-up">Căn hộ chung cư Bán</p>
                                    <p class="name-post"><a href="#">LOREM IPSUM DOLORIT </a></p>
                                    <p class="fs-15 font-400">21 Nguyễn Trung Ngạn, P. Bến Nghé, Q1</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="item-sidebar">
                        <div class="title-sidebar">DỰ ÁN NHIỀU NGƯỜI XEM</div>
                        <ul class="clearfix list-post">
                            <li>
                                <div class="wrap-item-post">
                                    <a href="#" class="rippler rippler-default">
                                        <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg"></div></div>
                                    </a>
                                    <p class="infor-by-up">Căn hộ chung cư Bán</p>
                                    <p class="name-post"><a href="#">LOREM IPSUM DOLORIT </a></p>
                                    <p class="fs-15 font-400">21 Nguyễn Trung Ngạn, P. Bến Nghé, Q1</p>
                                </div>
                            </li>
                            <li>
                                <div class="wrap-item-post">
                                    <a href="#" class="rippler rippler-default">
                                        <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg"></div></div>
                                    </a>
                                    <p class="infor-by-up">Căn hộ chung cư Bán</p>
                                    <p class="name-post"><a href="#">LOREM IPSUM DOLORIT </a></p>
                                    <p class="fs-15 font-400">21 Nguyễn Trung Ngạn, P. Bến Nghé, Q1</p>
                                </div>
                            </li>
                            <li>
                                <div class="wrap-item-post">
                                    <a href="#" class="rippler rippler-default">
                                        <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg"></div></div>
                                    </a>
                                    <p class="infor-by-up">Căn hộ chung cư Bán</p>
                                    <p class="name-post"><a href="#">LOREM IPSUM DOLORIT </a></p>
                                    <p class="fs-15 font-400">21 Nguyễn Trung Ngạn, P. Bến Nghé, Q1</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- <div class="col-xs-12 col-md-4 col-right sidebar-col">
                    <div class="title-sidebar">SIMILAR PROJECTS</div>
                    <ul class="clearfix list-post">
                        <li>
                            <div class="wrap-item-post">
                                <a href="#" class="rippler rippler-default">
                                    <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg">
                                    <input type="hidden" value="/store/ad/2016/03/03/480x360/56d7ac4535c48.jpg">
                                    </div></div>
                                    <div class="title-item">Căn hộ chung cư Bán</div>
                                </a>
                                <p class="date-post">Ngày đăng tin: <strong>12/2/2016, 8:30AM</strong></p>
                                <p class="name-post"><a href="#">Đường 10B, Xã Bình Chánh, Huyện Bình Chánh, Hồ Chí Minh</a></p>
                                <p class="id-duan">ID:<span>5090</span></p>
                                <ul class="clearfix list-attr-td">
                                    <li>
                                    <span class="icon icon-dt icon-dt-small"></span>58                                            
                                </li>
                                    <li>
                                    <span class="icon icon-bed icon-bed-small"></span>4                                            
                                </li>
                                    <li>
                                    <span class="icon icon-pt icon-pt-small"></span>4                                            
                                </li>
                                </ul>
                                <div class="bottom-item clearfix">
                                    <a href="#" class="pull-right fs-13">Chi tiết</a>
                                    <p>Giá <strong class="color-cd pdL-5">4,5 billion đồng</strong></p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="wrap-item-post">
                                <a href="#" class="rippler rippler-default">
                                    <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg">
                                    <input type="hidden" value="/store/ad/2016/03/03/480x360/56d7ac4535c48.jpg">
                                    </div></div>
                                    <div class="title-item">Căn hộ chung cư Bán</div>
                                </a>
                                <p class="date-post">Ngày đăng tin: <strong>12/2/2016, 8:30AM</strong></p>
                                <p class="name-post"><a href="#">Đường 10B, Xã Bình Chánh, Huyện Bình Chánh, Hồ Chí Minh</a></p>
                                <p class="id-duan">ID:<span>5090</span></p>
                                <ul class="clearfix list-attr-td">
                                    <li>
                                    <span class="icon icon-dt icon-dt-small"></span>58                                            
                                </li>
                                    <li>
                                    <span class="icon icon-bed icon-bed-small"></span>4                                            
                                </li>
                                    <li>
                                    <span class="icon icon-pt icon-pt-small"></span>4                                            
                                </li>
                                </ul>
                                <div class="bottom-item clearfix">
                                    <a href="#" class="pull-right fs-13">Chi tiết</a>
                                    <p>Giá <strong class="color-cd pdL-5">4,5 billion đồng</strong></p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="wrap-item-post">
                                <a href="#" class="rippler rippler-default">
                                    <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg">
                                    <input type="hidden" value="/store/ad/2016/03/03/480x360/56d7ac4535c48.jpg">
                                    </div></div>
                                    <div class="title-item">Căn hộ chung cư Bán</div>
                                </a>
                                <p class="date-post">Ngày đăng tin: <strong>12/2/2016, 8:30AM</strong></p>
                                <p class="name-post"><a href="#">Đường 10B, Xã Bình Chánh, Huyện Bình Chánh, Hồ Chí Minh</a></p>
                                <p class="id-duan">ID:<span>5090</span></p>
                                <ul class="clearfix list-attr-td">
                                    <li>
                                    <span class="icon icon-dt icon-dt-small"></span>58                                            
                                </li>
                                    <li>
                                    <span class="icon icon-bed icon-bed-small"></span>4                                            
                                </li>
                                    <li>
                                    <span class="icon icon-pt icon-pt-small"></span>4                                            
                                </li>
                                </ul>
                                <div class="bottom-item clearfix">
                                    <a href="#" class="pull-right fs-13">Chi tiết</a>
                                    <p>Giá <strong class="color-cd pdL-5">4,5 billion đồng</strong></p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="wrap-item-post">
                                <a href="#" class="rippler rippler-default">
                                    <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg">
                                    <input type="hidden" value="/store/ad/2016/03/03/480x360/56d7ac4535c48.jpg">
                                    </div></div>
                                    <div class="title-item">Căn hộ chung cư Bán</div>
                                </a>
                                <p class="date-post">Ngày đăng tin: <strong>12/2/2016, 8:30AM</strong></p>
                                <p class="name-post"><a href="#">Đường 10B, Xã Bình Chánh, Huyện Bình Chánh, Hồ Chí Minh</a></p>
                                <p class="id-duan">ID:<span>5090</span></p>
                                <ul class="clearfix list-attr-td">
                                    <li>
                                    <span class="icon icon-dt icon-dt-small"></span>58                                            
                                </li>
                                    <li>
                                    <span class="icon icon-bed icon-bed-small"></span>4                                            
                                </li>
                                    <li>
                                    <span class="icon icon-pt icon-pt-small"></span>4                                            
                                </li>
                                </ul>
                                <div class="bottom-item clearfix">
                                    <a href="#" class="pull-right fs-13">Chi tiết</a>
                                    <p>Giá <strong class="color-cd pdL-5">4,5 billion đồng</strong></p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="wrap-item-post">
                                <a href="#" class="rippler rippler-default">
                                    <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg">
                                    <input type="hidden" value="/store/ad/2016/03/03/480x360/56d7ac4535c48.jpg">
                                    </div></div>
                                    <div class="title-item">Căn hộ chung cư Bán</div>
                                </a>
                                <p class="date-post">Ngày đăng tin: <strong>12/2/2016, 8:30AM</strong></p>
                                <p class="name-post"><a href="#">Đường 10B, Xã Bình Chánh, Huyện Bình Chánh, Hồ Chí Minh</a></p>
                                <p class="id-duan">ID:<span>5090</span></p>
                                <ul class="clearfix list-attr-td">
                                    <li>
                                    <span class="icon icon-dt icon-dt-small"></span>58                                            
                                </li>
                                    <li>
                                    <span class="icon icon-bed icon-bed-small"></span>4                                            
                                </li>
                                    <li>
                                    <span class="icon icon-pt icon-pt-small"></span>4                                            
                                </li>
                                </ul>
                                <div class="bottom-item clearfix">
                                    <a href="#" class="pull-right fs-13">Chi tiết</a>
                                    <p>Giá <strong class="color-cd pdL-5">4,5 billion đồng</strong></p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="wrap-item-post">
                                <a href="#" class="rippler rippler-default">
                                    <div class="img-show"><div><img src="http://file4.batdongsan.com.vn/resize/350x280/2016/01/21/20160121171906-9f37.jpg">
                                    <input type="hidden" value="/store/ad/2016/03/03/480x360/56d7ac4535c48.jpg">
                                    </div></div>
                                    <div class="title-item">Căn hộ chung cư Bán</div>
                                </a>
                                <p class="date-post">Ngày đăng tin: <strong>12/2/2016, 8:30AM</strong></p>
                                <p class="name-post"><a href="#">Đường 10B, Xã Bình Chánh, Huyện Bình Chánh, Hồ Chí Minh</a></p>
                                <p class="id-duan">ID:<span>5090</span></p>
                                <ul class="clearfix list-attr-td">
                                    <li>
                                    <span class="icon icon-dt icon-dt-small"></span>58                                            
                                </li>
                                    <li>
                                    <span class="icon icon-bed icon-bed-small"></span>4                                            
                                </li>
                                    <li>
                                    <span class="icon icon-pt icon-pt-small"></span>4                                            
                                </li>
                                </ul>
                                <div class="bottom-item clearfix">
                                    <a href="#" class="pull-right fs-13">Chi tiết</a>
                                    <p>Giá <strong class="color-cd pdL-5">4,5 billion đồng</strong></p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div> -->
            </div>
        </div>
    </div>
</div>

<div id="popup-map" class="popup-common hide-popup">
    <div class="wrap-popup">
        <div class="inner-popup">
            <a href="#" class="btn-close-map"><?=Yii::t('project', 'Back')?></a>
            <div id="map" data-lat="<?= $model->lat ?>" data-lng="<?= $model->lng ?>"></div>
        </div>
    </div>
</div>

<div id="popup-share-social" class="popup-common hide-popup">
    <div class="wrap-popup">
        <div class="inner-popup">
            <a href="#" class="btn-close"><span class="icon icon-close"></span></a>
            <div class="wrap-body-popup">
                <span><?=Yii::t('project', 'Share on Social Network')?></span>
                <ul class="clearfix">
                    <li>
                        <a href="#" class="share-facebook">
                            <div class="circle"><div><span class="icon icon-face"></span></div></div>
                        </a>
                    </li>
                    <li>
                        <a href="#popup-email" class="email-btn">
                            <div class="circle"><div><span class="icon icon-email-1"></span></div></div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?=$this->renderAjax('/ad/_partials/shareEmail',[ 'project' => $model, 'yourEmail' => Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->email, 'recipientEmail' => '', 'params' => ['your_email' => false, 'setValueToEmail' => false] ])?>

<script type="text/javascript">
    $(document).ready(function () {
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            spaceBetween: 0,
            nextButton: '.swiper-button-next',
            prevButton: '.swiper-button-prev',
            loop: true
        });

        $('#popup-map').popupMobi({
            btnClickShow: ".icon-map-loca",
            closeBtn: "#popup-map .btn-close-map",
            effectShow: "show-hide",
            funCallBack: function() {
                var mapEl = $('#map');
                var latLng = {lat: Number(mapEl.data('lat')), lng:  Number(mapEl.data('lng'))};
                var map = new google.maps.Map(mapEl.get(0), {
                    center: latLng,
                    zoom: 16,
                    mapTypeControl: false,
                    zoomControl: true,
                    streetViewControl: false
                });

                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map
                });
            }
        });

        $('#popup-share-social').popupMobi({
            btnClickShow: ".icons-detail .icon-share-td",
            closeBtn: ".btn-close, .email-btn, .share-facebook",
            styleShow: "center"
        });

        $('#popup-email').popupMobi({
            btnClickShow: ".email-btn",
            closeBtn: '#popup-email .btn-cancel',
            styleShow: "full"
        });

        $(document).on('click', '.share-facebook', function() {
            FB.ui({
                method: 'share',
                href: '<?=Yii::$app->request->absoluteUrl?>'
            }, function(response){});
        });

    });
</script>