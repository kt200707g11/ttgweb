<?php
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use vsoft\ad\models\AdProduct;

$this->title = Yii::t('express','We offer exeptional amenities and renowned white - glove services');
$this->registerJsFile ( Yii::$app->view->theme->baseUrl . '/resources/js/gmap-v2.js', ['position' => View::POS_END]);
$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key=AIzaSyASTv_J_7DuXskr5SaCZ_7RVEw7oBKiHi4&callback=m2Map.loaded', ['depends' => ['yii\web\YiiAsset'], 'async' => true, 'defer' => true]);
$this->registerJsFile ( Yii::$app->view->theme->baseUrl . '/resources/js/lightbox.min.js', ['position' => View::POS_END]);
$this->registerJsFile ( Yii::$app->view->theme->baseUrl . '/resources/js/jquery.bxslider.js', ['position' => View::POS_END]);
$this->registerJsFile ( Yii::$app->view->theme->baseUrl . '/resources/js/string-helper.js', ['position' => View::POS_END]);
$this->registerJsFile ( Yii::$app->view->theme->baseUrl . '/resources/js/search-map.js', ['position' => View::POS_END]);
$this->registerJsFile ( Yii::$app->view->theme->baseUrl . '/resources/js/listing-share.js', ['position' => View::POS_END]);
$this->registerJsFile ( Yii::$app->view->theme->baseUrl . '/resources/js/search-results.js', ['position' => View::POS_END]);
$this->registerJsFile ( Yii::$app->view->theme->baseUrl . '/resources/js/geometries/1.js', ['position' => View::POS_END]);
$this->registerJs('var city = ' . json_encode($city) . ';', View::POS_BEGIN);
$this->registerJs("var types = " . json_encode(AdProduct::getAdTypes()) . ";", View::POS_BEGIN);
$this->registerJs('var productSaved = ' . json_encode($productSaved) . ';', View::POS_BEGIN);
$this->registerJs("var districtId = $districtId;", View::POS_BEGIN);
$this->registerJs("var initialZoom = $initialZoom;", View::POS_END);
$this->registerJs('var districts = ' . json_encode($districts) . ';', View::POS_BEGIN);
$this->registerJs('var wards = ' . json_encode($wards) . ';', View::POS_BEGIN);

$fb_appId = '680097282132293';
if(strpos(Yii::$app->urlManager->hostInfo, 'dev.metvuong.com'))
    $fb_appId = '736950189771012';
else if(strpos(Yii::$app->urlManager->hostInfo, 'local.metvuong.com'))
    $fb_appId = '891967050918314';
?>
<div class="list-filters-result">
	<form id="map-search-form" action="<?= Url::to('/real-estate/result') ?>" method="post">
		<input type="hidden" name="cityId" id="city-id" value="<?= Yii::$app->request->get('city') ?>" />
		<input type="hidden" name="districtId" id="district-id" value="<?= Yii::$app->request->get('district') ?>" />
		<input type="hidden" name="categoryId" id="category-id" value="<?= Yii::$app->request->get('category') ?>" />
		<input type="hidden" name="orderBy" id="order-by" value="created_at" />
		<input type="hidden" name="type" id="type" value="<?= Yii::$app->request->get('type') ?>" />
        <input class="hidden_filter" id="price-min-filter" type="hidden" name="costMin" value="<?= Yii::$app->request->get('costMin') ?>" />
        <input class="hidden_filter" id="price-max-filter" type="hidden" name="costMax" value="<?= Yii::$app->request->get('costMax') ?>" />
        <input class="hidden_filter" id="dt-min-filter" type="hidden" name="areaMin" value="<?= Yii::$app->request->get('areaMin') ?>" />
        <input class="hidden_filter" id="dt-max-filter" type="hidden" name="areaMax" value="<?= Yii::$app->request->get('areaMax') ?>" />
        <input class="hidden_filter" id="bed-filter" type="hidden" name="roomNo" value="<?= Yii::$app->request->get('roomNo') ?>" />
        <input class="hidden_filter" id="bath-filter" type="hidden" name="toiletNo" value="<?= Yii::$app->request->get('toiletNo') ?>" />

        <ul class="clearfix outsideevent list-filter">
            <li class="search-filter">
                <div class="box-search-header clearfix">
                    <div class="pull-left">
                        <div class="pull-left text-right mgT-10 list-tabs-search" style="display:none;">
                            <div data-active="1" data-tab="mua-thue" class="search-select active" data-step='step1'>
                                <a href="#">
                                    Buy/Rent
                                </a>
                            </div>
                        </div>
                        <?php $form = ActiveForm::begin([
                            'options'=>['class' => 'form-inline pull-left', 'method'=>'POST'],
                            'id'=>'search-kind',
                            'action'=>Url::to(['/ad/redirect']),
                            'fieldConfig' => [],
                        ]); ?>
                            <div class="form-group">
                                <div class="type-search">
                                    <div class="clearfix">
                                        <ul class="outsideevent"></ul>
                                        <input id="searchInput" name="search" type="text" class="form-control outsideevent" placeholder="" readonly="readonly">
                                    </div>
                                </div>

                                <div id="mua-thue" class="outsideevent search-wrap hidden-effect" data-step-title="Muốn Mua/Thuê ?">
                                    <div class="wrap-effect">
                                        <div class="search-item">
                                            <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                            <div class="wrap-step">
                                                <h3></h3>
                                                <ul class="clearfix">
                                                    <li data-id="1"><a href="#" data-item="mua-thue" data-slug-name data-next="tinh-thanh" data-prev>Muốn Mua</a></li>
                                                    <li data-id="2"><a href="#" data-item="mua-thue" data-slug-name data-next="tinh-thanh" data-prev>Muốn Thuê</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tinh-thanh" class="outsideevent search-wrap hidden-effect" data-step-title="Chọn Tỉnh/Thành ?">
                                    <div class="wrap-effect">
                                        <div class="search-item">
                                            <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                            <div class="wrap-step">
                                                <h3></h3>
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="quan-huyen" class="outsideevent search-wrap hidden-effect" data-step-title="Chọn Quận/Huyện ?">
                                    <div class="wrap-effect">
                                        <div class="search-item">
                                            <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                            <div class="wrap-step">
                                                <h3></h3>
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="loai-bds" class="outsideevent search-wrap hidden-effect" data-step-title="Chọn loại bất động sản ?">
                                    <div class="wrap-effect">
                                        <div class="search-item">
                                            <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                            <div class="wrap-step">
                                                <h3></h3>
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="loai-duan" class="outsideevent search-wrap hidden-effect" data-step-title="Chọn dự án ?">
                                    <div class="wrap-effect">
                                        <div class="search-item">
                                            <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                            <div class="wrap-step">
                                                <h3></h3>
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button id="btn-search" type="submit" class="btn btn-default icon">
                                <span><em class=""></em></span>
                            </button>
                            <input class="valInputHidden" id="valSearch" name="valSearch" type="hidden" value="">
                            <input class="valInputHidden" id="valTabActive" name="activeSearch" type="hidden" value="">
                            <input class="valInputHidden" id="valType" name="type" type="hidden" value="">
                            <input class="valInputHidden" id="valTinhThanh" name="city" type="hidden" value="">
                            <input class="valInputHidden" id="valQuanHuyen" name="district" type="hidden" value="">
                            <input class="valInputHidden" id="valLoaiBDS" name="category" type="hidden" value="">
                            <input class="valInputHidden" id="valDuAn" name="project" type="hidden" value="">
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </li>
	        <li>
	            <a href="#"><i class="pull-right"></i><span class="txt-tab">Giá</span><span class="txt-show"></span></a>
                <div class="filter-pane filter-common hidden-effect" data-filter="price-min-max">
                    <div class="wrap-effect">
                        <div id="minmax-entries" class="minmax-entries search-entry">
                            <div class="dualboxes">
                                <div class="box1">
                                    <input readonly="readonly" class="text commaFormat" maxlength="11" size="10" name="price-min" id="min-val" type="text" placeholder="Min">
                                </div>
                                <div class="dash">&nbsp;</div>
                                <div class="box2">
                                    <input readonly="readonly" class="text commaFormat" maxlength="11" size="11" name="price-max" id="max-val" type="text" placeholder="Max">
                                </div>
                            </div>
                        </div>
                        <div class="filter-minmax">
                            <div id="min-price-options" class="minmax-options min-price-options minmax" data-toggle-filter="min-val">
                                <ul class="dropdown-options search-entry">
                                    
                                </ul>
                            </div>
                            <div id="max-price-options" class="minmax-options max-price-options hide minmax" data-toggle-filter="max-val">
                                <ul class="dropdown-options search-entry">
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
	        </li>
	        <li>
	            <a href="#" data-symbol-unit="m<sup>2</sup>"><i class="pull-right"></i><span class="txt-tab">Diện tích</span><span class="txt-show"></span></a>
                <div class="filter-common filter-pane hidden-effect" data-filter="dt-min-max">
                    <div class="wrap-effect">
                        <div id="minmax-entries" class="minmax-entries search-entry">
                            <div class="dualboxes">
                                <div class="box1">
                                    <input readonly="readonly" class="text commaFormat" maxlength="11" size="10" name="dt-min" id="min-val" type="text" placeholder="Min">
                                </div>
                                <div class="dash">&nbsp;</div>
                                <div class="box2">
                                    <input readonly="readonly" class="text commaFormat" maxlength="11" size="11" name="dt-max" id="max-val" type="text" placeholder="Max">
                                </div>
                            </div>
                        </div>
                        <div class="filter-minmax">
                            <div id="min-dt-options" class="minmax-options min-dt-options minmax" data-toggle-filter="min-val">
                                <ul class="dropdown-options search-entry">
                                </ul>
                            </div>
                            <div id="max-dt-options" class="minmax-options max-dt-options hide minmax" data-toggle-filter="max-val">
                                <ul class="dropdown-options search-entry">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
	        </li>
	        <li>
	            <a href="#"><i class="pull-right"></i>Phòng ngủ<span class="txt-show"></span></a>
                <div class="filter-common filter-pane filter-bed filter-dropdown hidden-effect" data-filter="phong-ngu">
                    <div class="wrap-effect">
                        <div class="filter-bed">
                            <ul class="dropdown-options search-entry">
                                <li data-value="0"><a class="option">0+</a></li>
                                <li data-value="1"><a class="option">1</a></li>
                                <li data-value="2"><a class="option">2</a></li>
                                <li data-value="3"><a class="option">3</a></li>
                                <li data-value="4"><a class="option">4</a></li>
                                <li data-value="5"><a class="option">5</a></li>
                                <li data-value="6"><a class="option">6+</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
	        </li>
            <li>
                <a href="#"><i class="pull-right"></i>Phòng tắm<span class="txt-show"></span></a>
                <div class="filter-common filter-pane filter-bed filter-dropdown hidden-effect" data-filter="phong-tam">
                    <div class="wrap-effect">
                        <div class="filter-bed">
                            <ul class="dropdown-options search-entry">
                                <li data-value="0"><a class="option">0+</a></li>
                                <li data-value="1"><a class="option">1</a></li>
                                <li data-value="2"><a class="option">2</a></li>
                                <li data-value="3"><a class="option">3</a></li>
                                <li data-value="4"><a class="option">4</a></li>
                                <li data-value="5"><a class="option">5</a></li>
                                <li data-value="6"><a class="option">6+</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <a href="#"><i class="pull-right"></i>Khác</a>
                <div class="filter-common filter-pane filter-other hidden-effect">
                    <div class="wrap-effect">
                    		<div class="form-group">
                                <label for="" class="col-sm-4 control-label">Người đăng</label>
                                <div class="col-sm-8">
                                    <select class="form-control">
                                        <option>Tất cả</option>
                                        <option>Nhà môi giới</option>
                                        <option>Chính chủ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-4 control-label">Ngày đăng</label>
                                <div class="col-sm-8">
                                    <select id="time" name="time" class="form-control">
                                        <option value="">Bất kỳ</option>
                                        <option value="-1 day">1 ngày</option>
                                        <option value="-7 day">7 ngày</option>
                                        <option value="-14 day">14 ngày</option>
                                        <option value="-30 day">30 ngày</option>
                                        <option value="-60 day">60 ngày</option>
                                        <option value="-90 day">90 ngày</option>
                                        <option value="-6 month">6 tháng</option>
                                        <option value="-12 month">12 tháng</option>
                                        <option value="-24 month">24 tháng</option>
                                        <option value="-36 month">36 tháng</option>
                                    </select>
                                </div>
                            </div>
                    </div>
                </div>
            </li>
	        <li>
                <a href="#">Set Alert<em class="fa fa-heart"></em></a>
            </li>
            <!-- <li>
                <button id="submit-filter" type="button" class="btn btn-primary btn-sm btn-common"><em class="fa fa-filter"></em>Chọn lọc</button>
                <button id="reset-filter" type="button" class="btn btn-primary btn-sm btn-common">Reset</button>
            </li> -->
	    </ul>
    </form>
</div>
<div class="col-md-8 wrap-map-result" style="z-index: 0">
    <div class="container-map">
    	<div id="map" style="height: 100%;"></div>
    </div>
</div>
<div class="col-md-4 result-items">
	<div id="detail-wrap" style="background: #FFF;">
		<div id="map-loading" class="loader">
			<span class="round"></span>
			<span class="side s_left"><span class="fill"></span></span>
			<span class="side s_right"><span class="fill"></span></span>
		</div>
		<div id="detail-listing"></div></div>
    <div class="wrap-col-fixed-result clearfix" style="background: #FFFFFF">
        
        <!-- <h1 id="search-title" class="zsg-content_collapsed">Listings</h1>
        <span class="num-results"><span id="count-listing">0</span> results.</span> -->
        
        <ul id="order-by-tab" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a class="order-button" data-order="created_at" href="#" aria-controls="moi-nhat" role="tab" data-toggle="tab">Mới nhất</a></li>
            <li role="presentation"><a class="order-button" data-order="price" href="#" aria-controls="re-nhat" role="tab" data-toggle="tab">Rẻ nhất</a></li>
            <?php if(!Yii::$app->user->isGuest): ?>
            <li role="presentation"><a class="saved-listing" href="#" role="tab" data-toggle="tab" data-href="<?= Url::to(['saved-listing']) ?>">Đã lưu</a></li>
            <?php endif; ?>
            <li class="full-map"><a href="#">Full Map</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="moi-nhat">
                <div id="listing-loading" style="padding: 10px;">
                	<div class="loading_new_feed">          
                        <div class="_2iwr"></div>
                        <div class="_2iws"></div>
                        <div class="_2iwt"></div>
                        <div class="_2iwu"></div>
                        <div class="_2iwv"></div>
                        <div class="_2iww"></div>
                        <div class="_2iwx"></div>
                        <div class="_2iwy"></div>
                        <div class="_2iwz"></div>
                        <div class="_2iw-"></div>
                        <div class="_2iw_"></div>
                        <div class="_2ix0"></div>
                    </div>
                </div>
                <ul class="list-results clearfix"></ul>
                <div id="no-result" style="text-align: center; padding: 22px; display: none;"></div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="re-nhat">

            </div>
        </div>
    </div>
</div>
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
        js.async=true;
        js.src = "//connect.facebook.net/vi_VN/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<div class="modal fade" id="box-share-1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="icon"></span>
                </button>
                <h3>Chia sẻ</h3>
            </div>
            <div class="modal-body">
                <div class="wrap-modal clearfix">
                    
                    <?php
                    $share_form_1 = Yii::createObject([
                        'class'    => \frontend\models\ShareForm::className(),
                        'scenario' => 'share',
                    ]);

                    $f1 = ActiveForm::begin([
                        'id' => 'share_form_1',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => true,
                        'action' => Url::to(['/ad/sendmail'])
                    ]);
                    ?>
                    <div class="form-group">
                        <?= $f1->field($share_form_1, 'recipient_email')->textInput(['class'=>'form-control recipient_email', 'placeholder'=>Yii::t('recipient_email', 'Email người nhận...')]) ?>
                    </div>
                    <div class="form-group">
                        <?= $f1->field($share_form_1, 'your_email')->textInput(['class'=>'form-control your_email', 'placeholder'=>Yii::t('your_email', 'Email của bạn...')]) ?>
                    </div>
                    <div class="form-group">
                        <?= $f1->field($share_form_1, 'content')->textarea(['class'=>'form-control content', 'cols' => 30, 'rows' => 5, 'placeholder'=>Yii::t('content', 'Nội dung chia sẻ...')]) ?>
                    </div>
                    <?= $f1->field($share_form_1, 'address')->hiddenInput(['class' => '_address'])->label(false) ?>
                    <?= $f1->field($share_form_1, 'detailUrl')->hiddenInput(['class' => '_detailUrl'])->label(false) ?>
                    <?= $f1->field($share_form_1, 'domain')->hiddenInput(['class' => '_domain', 'value'=>Yii::$app->urlManager->getHostInfo()])->label(false) ?>
                    <div class="form-group">
                        <button type="button" class="btn btn-common send_mail">Gửi email</button>
                    </div>
                    <ul class="share-social clearfix">
                        <li>Chia sẻ mạng xã hội</li>
                        <li><a href="#" class="logo-social fb-icon"></a></li>
<!--                        <li><a href="#" class="logo-social twe-icon"></a></li>-->
<!--                        <li><a href="#" class="logo-social g-icon"></a></li>-->
                    </ul>
                    <?php $f1->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>