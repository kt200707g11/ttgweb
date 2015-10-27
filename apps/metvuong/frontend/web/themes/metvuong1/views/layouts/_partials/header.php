<header class="clearfix">
    <a href="#" class="logo-header pull-left"><img src="<?=Yii::$app->view->theme->baseUrl?>/resources/images/logo.png" alt="logo"></a>
    <div class="pull-right user-setting">
        <div class="dropdown select-lang">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                <span class="icon-lang-select lang-vi"></span>
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a class="icon-lang lang-en" href="#">ENGLISH</a></li>
                <li><a class="icon-lang lang-vi" href="#">VIETNAMESE</a></li>
            </ul>
        </div>
        <a class="user-option" href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Mr.Nguyen Ong</a>
        <a href="#">logout</a>
    </div>
    <div class="box-search-header clearfix">
        <div class="pull-left">

            <form class="form-inline pull-left" action="" id="search-kind">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Nhập địa điểm...">
                </div>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                        <span class="txt-selected">Loại</span>
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a data-value-id="1" data-value-text="Loại 1" href="#">Loại 1</a></li>
                        <li><a  data-value-id="2" data-value-text="Loại 2" href="#">Loại 2</a></li>
                        <li><a  data-value-id="3" data-value-text="Loại 3" href="#">Loại 3</a></li>
                    </ul>
                </div>
                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </form>
            <div class="pull-left text-right mgT-15 mgL-15">
                <div class="search-select active"><a href="#" class="icon-sale"></a></div>
                <div class="search-select"><a href="#" class="icon-cart"></a></div>
                <div class="search-select"><a href="#" class="icon-door"></a></div>
                <div class="search-select"><a href="#" class="icon-handhome"></a></div>
            </div>
        </div>
    </div>
    <a href="#" id="slide-menu-right"><em class="fa fa-reorder"></em></a>
</header>


<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#"><span class="glyphicon glyphicon-home"></span></a>
        </div>
        <div>
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Bất động sản  </a></li>
                <li><a href="#">Chứng khoán</a></li>
                <li><a href="#">Tài chính &amp; ngân hàng</a></li>
                <li><a href="#">Doanh Nghiệp</a></li>
                <li><a href="#">kinh tế vĩ mô</a></li>
                <li><a href="#">phong thủy</a></li>
            </ul>
        </div>
    </div>
</nav>
