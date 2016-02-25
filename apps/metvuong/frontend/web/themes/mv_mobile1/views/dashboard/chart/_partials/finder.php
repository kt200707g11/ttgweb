<?php
use yii\helpers\Url;
use frontend\models\Chart;

$data = Chart::find()->getDataFinder($id, $from, $to);
if(!empty($data)) {
$dataChart = $data['dataChart'];
$categories = $data['categories'];

ksort($dataChart);
$dataChart = array_values($dataChart);
ksort($categories);
$categories = array_values($categories);


?>
    <div id="chartAds" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <script>
        $(function () {
            $('#chartAds').highcharts({
                chart: {
                    type: 'column'
                },
                legend: {
                    enable: false
                },
                title: {
                    text: 'Lượt người tìm kiếm',
                    x: -20 //center
                },
                subtitle: {
                    text: 'Nguồn: MetVuong.com',
                    x: -20
                },
                xAxis: {
                    categories: <?=json_encode($categories);?>,
                    max: <?=!empty(count($categories)) ? count($categories) - 1 : 0?>
                },
                yAxis: {
                    title: {
                        text: 'Người tìm kiếm'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    useHTML:true,
                    formatter: function() {
                        var tooltip;
                        if (this.key == 'last') {
                            tooltip = '<b>Final result is </b> ' + this.y;
                        }
                        else {
                            tooltip = '<b>' + this.y + ' người tìm</b><br/>';
                        }
                        return tooltip;
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0,
                        borderWidth: 0
                    },
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {//alert ('Category: '+ this.category +', value: '+ this.y);
                                   $('#frmListVisit .wrap-modal').html('');
                                    var timer = 0;
                                    var _this = this;
                                    clearTimeout(timer);
                                    timer = setTimeout(function () {
                                        $.ajax({
                                            type: "get",
                                            dataType: 'html',
                                            url: _this.url,
                                            success: function (data) {
                                                $('#frmListVisit .wrap-modal').html($(data));
                                                //$('#frmListVisit').find('h3').html('Thống kê');
                                                $('#frmListVisit').find('.total').html(_this.y);
                                                $('#frmListVisit').find('.totalNext').html(_this.y - 3);
                                                $('#frmListVisit').find('.desTotal').html('Danh sách người tìm kiếm tin: <b>'+_this.series.name+'</b>');
                                            }
                                        });
                                    }, 500);
                                    $('#frmListVisit').modal();
                                }
                            }
                        }
                    }
                },
                series: <?=json_encode($dataChart);?>,
                credits: {
                    enabled: false
                }
            });
        });
    </script>
    <?php
}else {
    ?>
    <div class="alert alert-warning">
        <p class="text-center">Chưa có người nào tìm kiếm tin của bạn, bạn hãy <a href="">làm mới</a> tin của mình</p>
    </div>
    <?php
}?>
