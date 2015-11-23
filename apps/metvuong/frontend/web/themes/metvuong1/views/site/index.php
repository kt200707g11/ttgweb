<?php
use yii\web\View;
$this->registerCssFile(Yii::$app->view->theme->baseUrl."/resources/css/bootstrap.min.css", ['depends' => [\yii\bootstrap\BootstrapAsset::className()],], 'bootstrap');
$this->registerCssFile("https://fonts.googleapis.com/css?family=Roboto:400,300,700", ['depends' => [\yii\bootstrap\BootstrapAsset::className()],], 'font-roboto');
$this->registerCssFile(Yii::$app->view->theme->baseUrl."/resources/css/font-awesome.min.css", ['depends' => [\yii\bootstrap\BootstrapAsset::className()],], 'font-awesome');
$this->registerCssFile(Yii::$app->view->theme->baseUrl."/resources/css/simple-line-icons.css", ['depends' => [\yii\bootstrap\BootstrapAsset::className()],], 'simple-line-icons');
$this->registerCssFile(Yii::$app->view->theme->baseUrl."/resources/css/style-custom.css", ['depends' => [\yii\bootstrap\BootstrapAsset::className()],], 'style-custom');

Yii::$app->getView()->registerJsFile(Yii::$app->view->theme->baseUrl.'/resources/js/jquery.min.js', ['position'=>View::POS_HEAD]);
Yii::$app->getView()->registerJsFile(Yii::$app->view->theme->baseUrl.'/resources/js/bootstrap.min.js', ['position'=>View::POS_HEAD]);
Yii::$app->getView()->registerJsFile(Yii::$app->view->theme->baseUrl.'/resources/js/menu.min.js', ['position'=>View::POS_HEAD]);
$script = <<< JS
var url_tt = "_url_tt",
            url_loaibds = "_url_loaibds",
            url_ttuc = "_url_ttuc";
JS;
Yii::$app->getView()->registerJs(strtr($script, ['_url_tt'=>Yii::$app->view->theme->baseUrl.'/resources/data/tinh-thanh.json',
                                                '_url_loaibds'=>Yii::$app->view->theme->baseUrl.'/resources/data/loai-bds.json',
                                                '_url_ttuc'=>Yii::$app->view->theme->baseUrl.'/resources/data/loai-tintuc.json'
                                        ]), View::POS_HEAD);
Yii::$app->getView()->registerJsFile(Yii::$app->view->theme->baseUrl.'/resources/js/common.js', ['position'=>View::POS_HEAD]);
/* @var $this yii\web\View */

$this->title = Yii::t('express','We offer exeptional amenities and renowned white - glove services');
?>
<script>
    $(document).ready(function(){
        var urls = {0: false, 1: false, 2: {0: '<?=\yii\helpers\Url::to(['/news/index'])?>', 1: '<?=\yii\helpers\Url::to(['/building-project/index'])?>'}};
        $(document).on('click', '.btn.btn-default', function(){
            var idx = $('.search-select.active').index();
            if(urls[idx]){
                var classActive = $('.type-search ul li:first span').html();
                setTimeout(function() {
                    $('.wrap-search-home .logo-home').addClass('ani-logo');
                    $('.box-search-header').addClass('ani-search');
                    setTimeout(function() {
                        $('header').addClass('border-shadow');
                        if(classActive == 'Dự án'){
                            location.href = urls[idx][1];
                        }else if(classActive == 'Tin tức'){
                            location.href = urls[idx][0];
                        }
                    },500);
                },500);

            }else{
                alert('Coming Soon !');
            }
            return false;
        });
    });
</script>
<div class="o-wrapper clearfix wrap-page-home">
    <header class="home-page cd-secondary-nav">
        <div class="container clearfix">
            <ul class="pull-right menu-home">
                <li><a href="#"><em class="icon-plus"></em>Đăng tin</a></li>
                <li><a href="#"><em class="icon-user"></em>Đăng ký</a></li>
                <li><a href="#"><em class="icon-key"></em>Đăng nhập</a></li>
                <li class="lang-icon icon-en"><a href="#"></a></li>
                <li class="lang-icon icon-vi"><a href="#"></a></li>
            </ul>
            <div class="wrap-search-home">
                <div class="bgcover logo-home" style="background-image:url(<?=Yii::$app->view->theme->baseUrl?>/resources/images/logo.png);"><a href="#"></a></div>
                <div class="box-search-header clearfix">
                        <div class="pull-left">
                            <form class="form-inline pull-left" action="" id="search-kind">
                                <div class="form-group">
                                    <div class="type-search">
                                        <ul class="outsideevent"></ul>
                                        <input type="text" class="form-control outsideevent" placeholder="">
                                    </div>
                                    <div id="step-1" class="outsideevent search-wrap hidden-effect" data-txt-step="--Chọn Tỉnh/Thành--">
                                        <div class="wrap-effect">
                                            <div class="search-item">
                                                <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                                <h3>--Chọn Tỉnh/Thành phố--</h3>
                                                <ul class="clearfix list-tinh-thanh">
                                                    
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="step-2" class="outsideevent search-wrap hidden-effect" data-txt-step="--Chọn Quận/Huyện--">
                                        <div class="wrap-effect">
                                            <div class="search-item clearfix">
                                                <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                                <h3>--Chọn Quận/Huyện--</h3>
                                                <ul class="list-quan-huyen">
                                                    
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="step-3" class="outsideevent search-wrap hidden-effect" data-txt-step="--Chọn Loại--">
                                        <div class="wrap-effect">
                                            <div class="search-item clearfix">
                                                <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                                <h3>--Chọn loại nhà đất--</h3>
                                                <ul class="list-loai-bds">
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="step-4" class="outsideevent search-wrap hidden-effect" data-txt-step="Tìm kiếm...">
                                        <div class="wrap-effect">
                                            <div class="search-item clearfix">
                                                <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                                <div class="suggest-search-text">
                                                    <h3>--Gợi ý--</h3>
                                                    <div class="loading-suggest"></div>
                                                    <ul class="clearfix" style="display:none;">
                                                        <li><a href="#">Bè, Hồ Chí Minh Huyện <strong>Nhà</strong> Bè, Hồ Chí Minh<span>(1935 tin)</span></a></li>
                                                        <li><a href="#">Trang, Khánh Hòa Thành phố <strong>Nha</strong> Trang, Khánh Hòa<span>(1935 tin)</span></a></li>
                                                        <li><a href="#">Quận Thanh Xuân, Hà Nội Phường Nhân Chính, Quận Thanh Xuân, Hà Nội<span>(1935 tin)</span></a></li>
                                                        <li><a href="#">Thành phố <strong>Nha</strong> Trang, Khánh Hòa<span>(1935 tin)</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="step-5" data-step-box="fixed" class="outsideevent search-wrap hidden-effect" data-txt-step="--Chọn Loại Tin Tức--">
                                        <div class="wrap-effect">
                                            <div class="search-item clearfix">
                                                <a href="#" class="btn-close-search"><em class="icon-close"></em></a>
                                                <h3>--Chọn loại tin tức--</h3>
                                                <ul class="list-loai-tt">
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button id="btn-search" type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                                <input class="getValSuggest" type="hidden" id="valTT" value="">
                                <input class="getValSuggest" type="hidden" id="valQh" value="">
                                <input class="getValSuggest" type="hidden" id="valLoai" value="">
                                <input class="getValSuggest" type="hidden" id="valTTuc" value="">
                            </form>
                            <div class="pull-left text-right mgT-10 mgL-15">
                                <div class="search-select active">
                                    <a href="#" data-placeholder="--Chọn Tỉnh/Thành--" rel="#dd-search">
                                        <span>
                                            <em class="fa fa-home"></em>
                                            <em class="fa fa-search"></em>
                                        </span>
                                        <i>Mua Bán</i>
                                    </a>
                                </div>
                                <div class="search-select">
                                    <a href="#" data-placeholder="--Chọn Tỉnh/Thành--" rel="#dd-dky">
                                        <span>
                                            <em class="fa fa-home"></em>
                                            <em class="fa fa-usd"></em>
                                        </span>
                                        <i>Cho Thuê</i>
                                    </a>
                                </div>
                                <div class="search-select">
                                    <a href="#" class="" data-step-fix="step-5" data-placeholder="--Chọn Loại Tin Tức--" rel="#dd-news">
                                        <span>
                                            <em class="fa fa-home"></em>
                                            <em class="fa fa-file-text"></em>
                                        </span>
                                        <i>Tin Tức</i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </header>
    <div class="container">

    </div>
    <footer class="clearfix">
        <div class="pull-left copyright">
            <p><span>&copy;</span>2015. Bản quyền thuộc về Công ty Metvuong</p>
        </div>
        <div class="pull-right polli">
            <ul>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Điều khoản</a></li>
                <li>
                    <span>Kết nối:</span>
                    <a title="facebook metvuong.com" class="logo-social fb-icon" href="#"></a>
                    <a title="twitter metvuong.com" class="logo-social twe-icon" href="#"></a>
                    <a title="google plus metvuong.com" class="logo-social g-icon" href="#"></a>
                    <a title="youtube metvuong.com" class="logo-social ytu-icon" href="#"></a>
                </li>
            </ul>
        </div>
    </footer>
    <div id="iePopup">
        <div id="jr_overlay"></div>
        <div id="jr_wrap">
            <div id="jr_inner">
                <h1 id="jr_header">Bạn có biết rằng trình duyệt của bạn đã lỗi thời?</h1>
                <p>Trình duyệt của bạn đã lỗi thời, và có thể không tương thích tốt với website, chắc chắn rằng trải nghiệm của bạn trên website sẽ bị hạn chế. Bên dưới là danh sách những trình duyệt phổ biến hiện nay.</p>
                <p>Click vào biểu tượng để tải trình duyệt bạn muốn.</p>
                <ul>
                    <li id="jr_chrome"><a href="http://www.google.com/chrome/" target="_blank">Chrome 34</a></li>
                    <li id="jr_firefox"><a href="http://www.mozilla.com/firefox/" target="_blank">Firefox 29</a></li>
                    <li id="jr_msie"><a href="http://www.microsoft.com/windows/Internet-explorer/" target="_blank">Internet Explorer 10</a></li>
                    <li id="jr_opera"><a href="http://www.opera.com/download/" target="_blank">Opera 20</a></li>
                    <li id="jr_safari"><a href="http://www.apple.com/safari/download/" target="_blank">Safari</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>