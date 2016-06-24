<?php 
	use yii\helpers\Url;
	use vsoft\ad\models\AdCategoryGroup;
	use yii\helpers\Html;
	use vsoft\ad\models\AdProduct;
	use yii\web\View;
	
	$db = Yii::$app->getDb();
	
	$hideSearchForm = Yii::$app->request->get();
	
	$compress = isset(Yii::$app->params['local']) ? '' : '.compress';
	
	$resourceListingMap = Yii::$app->view->theme->baseUrl . '/resources/js/map' . $compress . '.js';
	$resourceHistoryJs = Yii::$app->view->theme->baseUrl . '/resources/js/history.js';
	$resourceApi = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyASTv_J_7DuXskr5SaCZ_7RVEw7oBKiHi4&callback=desktop.loadedResource&libraries=geometry';
	
	$autoFillValue = $searchModel->getAutoFillValue();
	$actionId = ($searchModel->type == AdProduct::TYPE_FOR_SELL) ? Yii::t('url', 'nha-dat-ban') : Yii::t('url', 'nha-dat-cho-thue');
	$loadProjectUrl = Url::to(['/ad/get-project']);
	
	$script = <<<EOD
	var resources = ['$resourceHistoryJs', '$resourceListingMap', '$resourceApi'];
	var actionId = '$actionId';
	var loadProjectUrl = '$loadProjectUrl';
EOD;
	
	$this->registerCssFile(Yii::$app->view->theme->baseUrl.'/resources/css/map.css');
	$this->registerJs($script, View::POS_BEGIN);
	$this->registerJsFile(Yii::$app->view->theme->baseUrl.'/resources/js/string-helper.js', ['position'=>View::POS_HEAD]);
	$this->registerJsFile(Yii::$app->view->theme->baseUrl . '/resources/js/listing' . $compress . '.js', ['position' => View::POS_END]);
	$this->registerJsFile(Yii::$app->view->theme->baseUrl.'/resources/js/swiper.jquery.min.js', ['position'=>View::POS_END]);
	$this->registerJsFile(Yii::$app->view->theme->baseUrl.'/resources/js/jquery.rateit.js', ['position'=>View::POS_END]);
	$this->registerJsFile(Yii::$app->view->theme->baseUrl.'/resources/js/clipboard.min.js', ['position'=>View::POS_END]);

    $cookies = Yii::$app->request->cookies;
    $cookie = $cookies->getValue('tutorial');

if(Yii::$app->controller->action->actionMethod == 'actionIndex1') {
    if (!isset($cookie['colisting']) || empty($cookie['colisting'])) {
        ?>
        <script>
            $(document).ready(function () {
                var txtTour = ["<?=Yii::t('tutorial',"Chào mừng bạn đến với trang Cần Mua, mục này và mục Cần Thuê có chức năng giống nhau, để tìm sản phẩm theo tên thành phố, quận, phường, đường, mã số… bạn hãy gõ vào thanh tìm kiếm ngay hàng đầu và kết quả sẽ tự động thay đổi khi bạn thay đổi thông tin tìm kiếm.")?>",
                    "<?=Yii::t('tutorial',"Bản đồ lớn sẽ cho phép bạn nhìn thấy địa điểm trong danh sách tiềm năng của bạn, bạn có thể nhấp vào để xem cụ thể các địa điểm.")?>",
                    "<?=Yii::t('tutorial',"Các danh sách bên phải là danh sách sản phẩm được đề xuất có kết quả gần nhất với các yêu cầu tìm kiếm, được phân loại theo cách đánh giá của MetVuong để đảm bảo chất lượng tin cũng như sự liên quan đến yêu cầu của bạn.")?>"];
                var intro = $.hemiIntro({
                    debug: false,
                    steps: [
                        {
                            selector: ".toggle-search",
                            placement: "bottom",
                            content: txtTour[0]
                        }, {
                            selector: "#map-wrap",
                            placement: "right",
                            content: txtTour[1]
                        }, {
                            selector: ".wrap-listing",
                            placement: "left",
                            content: txtTour[2]
                        }
                    ],
                    onComplete: function (item) {
                        $.ajax({
                            type: "get",
                            dataType: 'json',
                            url: '<?=Url::to(['site/set-cookie'])?>?name=colisting',
                            success: function (data) {
                            }
                        });
                    }
                });

                intro.start();
            });
        </script>
        <?php
        $this->registerJsFile(Yii::$app->view->theme->baseUrl . '/resources/js/tour-intro.min.js', ['position' => View::POS_END]);
    }
} else if(Yii::$app->controller->action->actionMethod == 'actionIndex2') {
    if (!isset($cookie['coRentListing']) || empty($cookie['coRentListing'])) {
        ?>
        <script>
            $(document).ready(function () {
                var txtTour = ["<?=Yii::t('tutorial',"Chào mừng bạn đến với trang Cần Thuê, mục này và mục Cần Mua có chức năng giống nhau, để tìm sản phẩm theo tên thành phố, quận, phường, đường, mã số… bạn hãy gõ vào thanh tìm kiếm ngay hàng đầu và kết quả sẽ tự động thay đổi khi bạn thay đổi thông tin tìm kiếm.")?>",
                    "<?=Yii::t('tutorial',"Bản đồ lớn sẽ cho phép bạn nhìn thấy địa điểm trong danh sách tiềm năng của bạn, bạn có thể nhấp vào để xem cụ thể các địa điểm.")?>",
                    "<?=Yii::t('tutorial',"Các danh sách bên phải là danh sách sản phẩm được đề xuất có kết quả gần nhất với các yêu cầu tìm kiếm, được phân loại theo cách đánh giá của MetVuong để đảm bảo chất lượng tin cũng như sự liên quan đến yêu cầu của bạn.")?>"];
                var intro = $.hemiIntro({
                    debug: false,
                    steps: [
                        {
                            selector: ".toggle-search",
                            placement: "bottom",
                            content: txtTour[0]
                        }, {
                            selector: "#map-wrap",
                            placement: "right",
                            content: txtTour[1]
                        }, {
                            selector: ".wrap-listing",
                            placement: "left",
                            content: txtTour[2]
                        }
                    ],
                    onComplete: function (item) {
                        $.ajax({
                            type: "get",
                            dataType: 'json',
                            url: '<?=Url::to(['site/set-cookie'])?>?name=coRentListing',
                            success: function (data) {
                            }
                        });
                    }
                });

                intro.start();
            });
        </script>
        <?php
        $this->registerJsFile(Yii::$app->view->theme->baseUrl . '/resources/js/tour-intro.min.js', ['position' => View::POS_END]);
    }
}
?>
<div class="result-listing clearfix">
	<div class="wrap-listing-item">
		<div class="items-list">
			<div class="inner-wrap">
				<form id="search-form" action="/<?= $actionId ?>" method="get">
					<div class="search-subpage">
						<div class="advande-search clearfix">
							<div class="toggle-search"<?= $hideSearchForm ? ' style="display: none"' : '' ?>>
								<div class="frm-item select-location show-num-frm">
									<div id="map-search-wrap">
										<input class="exclude" type="text" id="map-search" value="<?= $autoFillValue ?>" data-val="<?= $autoFillValue ?>" autocomplete="off" />
										<div id="search-list" class="hide">
											<div class="hint-wrap">
												<div class="hint"><?= Yii::t('ad', 'Nhập tên dự án, đường, phường, quận hoặc thành phố.') ?></div>
												<div class="center"><?= Yii::t('ad', 'Tìm kiếm gần đây') ?></div>
											</div>
											<ul></ul>
										</div>
										<?= Html::activeHiddenInput($searchModel, 'city_id', ['class' => 'auto-fill']); ?>
										<?= Html::activeHiddenInput($searchModel, 'district_id', ['class' => 'auto-fill']); ?>
										<?= Html::activeHiddenInput($searchModel, 'ward_id', ['class' => 'auto-fill']); ?>
										<?= Html::activeHiddenInput($searchModel, 'street_id', ['class' => 'auto-fill']); ?>
										<?= Html::activeHiddenInput($searchModel, 'project_building_id', ['class' => 'auto-fill']); ?>
									</div>
								</div>
								<div class="frm-item select-loaibds">
									<div class="box-dropdown dropdown-common">
										<div class="val-selected style-click">
											<span class="selected" data-placeholder="<?= Yii::t('ad', 'Property Types') ?>"><?= Yii::t('ad', 'Property Types') ?></span>
											<span class="arrowDownFillFull"></span>
										</div>
										<div class="item-dropdown hide-dropdown">
											<ul class="clearfix loai-bds">
												<li><a href="#" data-value="" data-order="3"><?= Yii::t('ad', 'Property Types') ?></a></li>
												<?php
													$groupCategories = $db->cache(function(){
														return AdCategoryGroup::find()->all();
													});
													
													foreach ($groupCategories as $groupCategory):
												?>
												<li><a href="#" data-value="<?= implode(',', $groupCategory->categories_id) ?>" data-order="3"><?= Yii::t('ad', $groupCategory['name']) ?></a></li>
												<?php endforeach; ?>
											</ul>
											<?= Html::activeHiddenInput($searchModel, 'category_id', ['id' => 'loai-bds']); ?>
										</div>
									</div>
								</div>
								<div class="frm-item num-phongngu">
									<div class="box-dropdown dropdown-common">
										<div class="val-selected style-click" data-text-add="<?= Yii::t('ad', 'Beds') ?>"><span class="selected">0+ <?= Yii::t('ad', 'Beds') ?></span><span class="arrowDownFillFull"></span></div>
										<div class="item-dropdown item-bed-bath hide-dropdown">
											<ul class="clearfix">
												<li><a href="#" data-value="">0+</a></li>
												<li><a href="#" data-value="1">1+</a></li>
												<li><a href="#" data-value="2">2+</a></li>
												<li><a href="#" data-value="3">3+</a></li>
												<li><a href="#" data-value="4">4+</a></li>
												<li><a href="#" data-value="5">5+</a></li>
												<li><a href="#" data-value="6">6+</a></li>
												<li><a href="#" data-value="7">7+</a></li>
												<li><a href="#" data-value="8">8+</a></li>
												<li><a href="#" data-value="9">9+</a></li>
												<li><a href="#" data-value="10">10+</a></li>
											</ul>
											<?= Html::activeHiddenInput($searchModel, 'room_no'); ?>
										</div>
									</div>
								</div>
								<div class="frm-item num-phongtam">
									<div class="box-dropdown dropdown-common">
										<div class="val-selected style-click" data-text-add="<?= Yii::t('ad', 'Bath') ?>"><span class="selected">0+ <?= Yii::t('ad', 'Bath') ?></span><span class="arrowDownFillFull"></span></div>
										<div class="item-dropdown item-bed-bath hide-dropdown">
											<ul class="clearfix">
												<li><a href="#" data-value="">0+</a></li>
												<li><a href="#" data-value="1">1+</a></li>
												<li><a href="#" data-value="2">2+</a></li>
												<li><a href="#" data-value="3">3+</a></li>
												<li><a href="#" data-value="4">4+</a></li>
												<li><a href="#" data-value="5">5+</a></li>
												<li><a href="#" data-value="6">6+</a></li>
												<li><a href="#" data-value="7">7+</a></li>
												<li><a href="#" data-value="8">8+</a></li>
												<li><a href="#" data-value="9">9+</a></li>
												<li><a href="#" data-value="10">10+</a></li>
											</ul>
											<?= Html::activeHiddenInput($searchModel, 'toilet_no'); ?>
										</div>
									</div>
								</div>
								<div class="frm-item choice_price_dt select-price">
									<div class="box-dropdown" data-item-minmax="prices">
										<div class="val-selected style-click price-search">
											<span class="txt-holder-minmax"><?= Yii::t('ad', 'Price') ?> </span>
											<div>
												<span class="wrap-min"></span>
												<span class="trolen">+</span>
												<span class="den">-</span>
												<span class="wrap-max"></span>
												<span class="troxuong"><?= Yii::t('ad', 'below') ?></span>
											</div>
											<span class="arrowDownFillFull"></span>
										</div>
										<div class="item-dropdown hide-dropdown wrap-min-max">
											<div class="box-input clearfix">
												<span class="txt-min min-max active min-val" data-value="" data-text="<?= Yii::t('ad', 'Min') ?>"><?= Yii::t('ad', 'Min') ?></span>
												<span class="text-center"><span></span></span>
												<span class="txt-max min-max max-val" data-value="" data-text="<?= Yii::t('ad', 'Max') ?>"><?= Yii::t('ad', 'Max') ?></span>
												<?= Html::activeHiddenInput($searchModel, 'price_min', ['id' => 'priceMin']); ?>
												<?= Html::activeHiddenInput($searchModel, 'price_max', ['id' => 'priceMax']); ?>
											</div>
											<div class="filter-minmax clearfix">
												<ul data-wrap-minmax="min-val" class="wrap-minmax clearfix"></ul>
												<ul data-wrap-minmax="max-val" class="wrap-minmax clearfix"></ul>
											</div>
										</div>
									</div>
								</div>
								<div class="frm-item choice_price_dt select-dt">
									<div class="box-dropdown" data-item-minmax="area">
										<div class="val-selected style-click dt-search">
											<span class="txt-holder-minmax"><?= Yii::t('ad', 'Size') ?></span>
											<div>
												<span class="wrap-min"></span>
												<span class="trolen">+</span>
												<span class="den">-</span>
												<span class="wrap-max"></span>
												<span class="troxuong"><?= Yii::t('ad', 'below') ?></span>
											</div>
											<span class="arrowDownFillFull"></span>
										</div>
										<div class="item-dropdown hide-dropdown wrap-min-max">
											<div class="box-input clearfix">
												<span class="txt-min min-max active min-val" data-value="" data-text="<?= Yii::t('ad', 'Min') ?>"><?= Yii::t('ad', 'Min') ?></span>
												<span class="text-center"><span></span></span>
												<span class="txt-max min-max max-val" data-value="" data-text="<?= Yii::t('ad', 'Max') ?>"><?= Yii::t('ad', 'Max') ?></span>
												<?= Html::activeHiddenInput($searchModel, 'size_min', ['id' => 'dtMin']); ?>
												<?= Html::activeHiddenInput($searchModel, 'size_max', ['id' => 'dtMax']); ?>
											</div>
											<div class="filter-minmax clearfix">
												<ul data-wrap-minmax="min-val" class="wrap-minmax"></ul>
												<ul data-wrap-minmax="max-val" class="wrap-minmax"></ul>
											</div>
										</div>
									</div>
								</div>
								<div class="frm-item select-others show-num-frm">
									<div class="box-dropdown dropdown-common">
										<div class="val-selected style-click">
											<span class="selected" data-placeholder="Loại bất động sản?"><?= Yii::t('ad', 'More') ?></span>
											<span class="arrowDownFillFull"></span>
										</div>
										<div class="item-dropdown hide-dropdown">
											<?= Html::activeHiddenInput($searchModel, 'type', ['name' => '']); ?>
											<div class="form-group col-xs-12 col-sm-6">
												<div class="">
													<?php 
														$items = [AdProduct::OWNER_HOST => Yii::t('ad', 'Owner'), AdProduct::OWNER_AGENT => Yii::t('ad', 'Agent')];
													?>
													<?= Html::activeDropDownList($searchModel, 'owner', $items, ['prompt' => Yii::t('ad', Yii::t('ad', 'Owner') . '/' . Yii::t('ad', 'Agent')), 'class' => 'form-control']) ?>
												</div>
											</div>
											<div class="form-group col-xs-12 col-sm-6">
												<div class="">
													<?php
														$tDays = Yii::t('ad', 'days');
														$tMonth = Yii::t('ad', 'month');
														$items = [
															"-1 day" => "1 " . Yii::t('ad', 'day'),
															"-7 day" => "7 " . $tDays,
															"-14 day" => "14 " . $tDays,
															"-30 day" => "30 " . $tDays,
															"-60 day" => "60 " . $tDays,
															"-90 day" => "90 " . $tDays,
															"-6 month" => "6 " . $tMonth,
															"-12 month" => "12 " . $tMonth,
															"-24 month" => "24 " . $tMonth,
															"-36 month" => "36 " . $tMonth,
														];
													?>
													<?= Html::activeDropDownList($searchModel, 'created_before', $items, ['prompt' => Yii::t('ad', 'Time posted'), 'class' => 'form-control']) ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<button class="btn-submit btn-common <?= $hideSearchForm ? '' : 'active' ?>"><?= Yii::t('ad', 'Search') ?></button>
						</div>
					</div>
					<div id="project-info">
						<?php if($searchModel->project_building_id): ?>
						<?= $this->render('_partials/projectInfo', ['project' => $searchModel->projectBuilding]) ?>
						<?php endif; ?>
					</div>
					<div id="sort" class="dropdown-select option-show-listing clearfix">
						<div class="val-selected style-click">
							<?php
								$items = [
									'-score' => Yii::t('ad', 'Point'),
									'-updated_at' => Yii::t('ad', 'Newest'),
									'-price' =>Yii::t('ad', 'Price (High to Low)'),
									'price' =>Yii::t('ad', 'Price (Low to Hight)'),
								];
							?>
							<?= Html::activeDropDownList($searchModel, 'order_by', $items, ['prompt' => Yii::t('ad', 'Sort by'), 'class' => 'form-control']) ?>
						</div>
					</div>
					<div style="display: none;" id="af-wrap">
						<?= Html::activeHiddenInput($searchModel, 'rm', ['disabled' => true]); ?>
						<input type="hidden" id="rl" name="rl" />
						<?= Html::activeHiddenInput($searchModel, 'ra', ['disabled' => true]); ?>
						<?= Html::activeHiddenInput($searchModel, 'rect', ['disabled' => true]); ?>
						<?= Html::activeHiddenInput($searchModel, 'ra_k', ['disabled' => true]); ?>
						<?= Html::activeHiddenInput($searchModel, 'iz', ['disabled' => true]); ?>
						<?= Html::activeHiddenInput($searchModel, 'z', ['disabled' => true]); ?>
						<?= Html::activeHiddenInput($searchModel, 'c', ['disabled' => true]); ?>
						<?= Html::activeHiddenInput($searchModel, 'did', ['disabled' => true]); ?>
						<?= Html::activeHiddenInput($searchModel, 'page', ['disabled' => true]); ?>
					</div>
				</form>
				<div class="listing-contai">
					<div class="wrap-listing clearfix">
						<div id="content-holder"><?= $this->render('_partials/side-list', ['searchModel' => $searchModel, 'list' => $list]) ?></div>
						<div id="loading-list" class="loading-proccess" style="display: none; position: absolute;top: 0px;left: 0px;right: 0;bottom: 0;z-index: 999;background: rgba(0, 0, 0, 0.5);"><span></span></div>
					</div>
				</div>
			</div>
			<div class="detail-listing-dt">
				<div class="inner-detail-listing">
					<div class="bar-top-detail">
						<a href="#" class="close-slide-detail font-700 fs-14 color-cd-hover"><span class="icon-mv fs-12 mgR-5"><span class="icon-close-icon"></span></span><?= Yii::t('ad', 'Close') ?></a>
						<a href="#" class="btn-extra font-700 fs-14 color-cd-hover" target="_blank"><span class="icon-mv fs-14 mgR-5"><span class="icon-up-arrow"></span></span><?= Yii::t('ad', 'Expand') ?></a>
					</div>
					<div class="detail-listing" style="position: relative;">
						<div class="container"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="wrap-map-listing">
		<div id="map-wrap" class="inner-wrap">
			<div id="map" class="inner-wrap"></div>
			<div id="progress-bar"><div id="progress-bar-p"></div></div>
		</div>
	</div>
</div>