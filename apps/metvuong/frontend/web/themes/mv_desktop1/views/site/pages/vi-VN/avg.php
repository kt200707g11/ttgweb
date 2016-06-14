<?php
/**
 * Created by PhpStorm.
 * User: vinhnguyen
 * Date: 4/13/2016
 * Time: 9:52 AM
 */
use yii\helpers\Html;
use vsoft\ad\models\AdCity;
use vsoft\ad\models\AdDistrict;
use yii\helpers\ArrayHelper;
use yii\web\View;

$this->registerCssFile(Yii::$app->view->theme->baseUrl . '/resources/css/select2.min.css');
$this->registerJsFile(Yii::$app->view->theme->baseUrl . '/resources/js/jquery-ui.min.js', ['position' => View::POS_END]);
$this->registerJsFile(Yii::$app->view->theme->baseUrl . '/resources/js/select2.full.min.js', ['position' => View::POS_END]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/df-number-format/2.1.6/jquery.number.min.js', ['position' => View::POS_END]);


$cities = AdCity::find()->all();
$citiesDropdown = ArrayHelper::map($cities, 'id', 'name');
$district = AdDistrict::find()->all();
$districtDropdown = ArrayHelper::map($district, 'id', 'name');
?>
<div class="title-fixed-wrap container">
    <div class="tool-cacu">
        <div class="wrap-frm-listing">
            <div class="group-frm">
                <form id="frmAvg">
                    <div class="title-frm">Thành phố / Quận-Huyện / Phường-Xã</div>
                    <div class="row region">
                        <div class="form-group col-xs-12 col-sm-6">
                            <label>Thành Phố </label>
                            <?=Html::dropDownList('city', null, $citiesDropdown, ['class' => 'form-control search region_city', 'prompt' => "..."])?>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label>Quận </label>
                            <?=Html::dropDownList('district', null, $districtDropdown, ['class' => 'form-control search region_district', 'prompt' => "..."])?>
                        </div>
                    </div>
                    <a href="#" class="btn-form btn-common btn-tinhnhanh"> Tính nhanh <span class="arrow-icon"> </span> </a>
                </form>
            </div>
            <div class="tool-hdr black-hdr"> Kết quả</div>
            <article id="inKetQua">

            </article>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var func = {
            appendDropdown: function(el, items) {
                el.find("option:not(:first-child)").remove();
                for(var i in items) {
                    if(items[i]['pre']) {
                        el.append('<option data-pre="' + items[i]['pre'] + '" value="' + items[i]['id'] + '">' + items[i]['name'] + '</option>');
                    } else {
                        el.append('<option value="' + items[i]['id'] + '">' + items[i]['name'] + '</option>');
                    }
                }

                el.select2();
            },
        };
        $('.region_city').select2();



        $(document).on('change', '.region_city', function (e) {
            var form = $('.region');
            if($(this).val()){
                $.get('/listing/list-district', {cityId: $(this).val()}, function(districts){
                    func.appendDropdown($('.region_district'), districts);
                });
            }
        });

        $(document).on('click', '.btn-tinhnhanh', function (e) {
            if($('.region_city').val()) {
                $.post('/site/avg', $('#frmAvg').serialize(), function (response) {
                    var html = '<table class="savings-tbl"><tbody><tr class="savings-tlt"><td>Đơn vị hành chính</td><td>Avg</td></tr>';
                    var text = $('.region_city option:selected').text();
                    if($('.region_district').val()){
                        text += ', '+$('.region_district option:selected').text()
                    }
                    var avg = response.sum / response.total;
                    html += '<tr><td class="saving_table saving_table_left">' + text + '</td><td class="saving_table">' + $.number(avg) + ' VND</td></tr>';
                    html += '</tbody></table>';
                    $('#inKetQua').html(html);
                });
            }else{
                alert('Vui lòng chọn Thành Phố');
            }
        });
    });
</script>