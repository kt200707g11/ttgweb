<?php
use yii\helpers\Url;
?>
<div class="col-xs-3 infor-user-profile">
    <div class="inner-infor">
        <div class="title-box">
            <h2>Trang cá nhân</h2>
        </div>
        <div class="clearfix list-profile">
            <div class="item-box mgB-15">
                <ul class="clearfix">
                    <li><a href="<?=Url::to(['user-management/chart'])?>" class="partial">Thống kê</a></li>
                </ul>
            </div>
            <div class="item-box mgB-15">
                <div class="title-box">Bất động sản của bạn</div>
                <ul class="clearfix">
                    <li><a href="<?=Url::to(['user-management/ad'])?>" class="partial">Quản lý tin rao bán/cho thuê</a></li>
                </ul>
            </div>
            <div class="item-box mgB-15">
                <div class="title-box">Quản lý thông tin cá nhân</div>
                <ul class="clearfix">
                    <li><a href="<?=Url::to(['user-management/profile'])?>" class="partial">Thay đổi thông tin cá nhân</a></li>
                    <li><a href="<?=Url::to(['user-management/password'])?>" class="partial">Thay đổi mật khẩu</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var timer = 0;
        $(document).on('click', '.list-profile li a.partial', function(){
            var _this = $(this);
            clearTimeout(timer);
            timer = setTimeout(function() {
                $.ajax({
                    type: "get",
                    dataType: 'html',
                    url: _this.attr('href'),
                    success: function(data) {
                        if(data){
                            $('.user_management').html(data);
                            $('.right-profile').hide();
                            $('.right-profile').toggle( "slide" );
                        }
                    }
                });
            }, 500);
            return false;
        });
    });
</script>