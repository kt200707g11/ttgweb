<?php
use yii\web\View;
use yii\helpers\Url;
?>
<div class="title-fixed-wrap">
    <div class="container">
        <div class="statis">
            <div class="title-top">
                <?=Yii::t('report','Report')?>
            </div>
        	<section class="clearfix mgB-40">
                <div class="pull-right fs-13 mgB-15">
                    <div class="clearfix d-ib ver-c">
                        <a href="<?= Url::to(['report/index', 'filter'=>'week'], true) ?>" class="show-view-chart<?=($filter=='week' ? ' active' : '')?>"><?=Yii::t('statistic','Week')?></a>
                        <a href="<?= Url::to(['report/index', 'filter'=>'2week'], true) ?>" class="show-view-chart<?=($filter=='2week' ? ' active' : '')?>"><?=Yii::t('statistic','Two weeks')?></a>
                        <a href="<?= Url::to(['report/index', 'filter'=>'month'], true) ?>" class="show-view-chart<?=($filter=='month' ? ' active' : '')?>"><?=Yii::t('statistic','Month')?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
        		<div class="summary clearfix">
                    <div class="wrap-chart clearfix">
        				<div class="wrap-img">
                            <div class="wrapChart">
                                <?=$this->render('/report/default/_partials/chart', ['categories'=>$categories, 'dataChart'=>$dataChart]);?>
                            </div>
                        </div>
        			</div>
                    <ul class="option-view-stats clearfix">
                        <li>
                            <input type="checkbox" name="toggle-chart" value="" id="register" checked><label for="register"><?=Yii::t('report','Register')?></label>
                        </li>
                        <li>
                            <input type="checkbox" name="toggle-chart" value="" id="login" checked><label for="login"><?=Yii::t('report','Login')?></label>
                        </li>
                        <li>
                            <input type="checkbox" name="toggle-chart" value="" id="listing" checked><label for="listing"><?=Yii::t('report','Listing')?></label>
                        </li>
                        <li style="display: none;">
                            <input type="checkbox" name="toggle-chart" value="" id="transaction" checked><label for="transaction"><?=Yii::t('report','Transaction')?></label>
                        </li>
                    </ul>
        		</div>
        	</section>
        </div>
    </div>
</div>
<script src="//code.highcharts.com/highcharts.js"></script>
<script>
    $(document).ready(function () {
        var chart = $('#chartAds').highcharts();
        $(document).on('click', '.option-view-stats input', function (e) {
            for ( var i = 0; i < chart.series.length; i++ ) {
                chart.series[i].hide();
            }
            $('.option-view-stats input[type=checkbox]').each(function () {
                if (this.checked) {
                    var index = $(this).parent().index();
                    chart.series[index].show();
                }
            });

        });
    });
</script>