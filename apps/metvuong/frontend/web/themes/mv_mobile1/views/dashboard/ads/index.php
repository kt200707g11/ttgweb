<?php
use yii\web\View;
use yii\helpers\Url;
?>

<div class="u-allduan">
	<div class="title-top">Tất cả dự án</div>
	<div class="no-duan hide">
		<div>
			<p>Hiện tại, bạn không có<br>dự án nào.</p>
			<a href="#" class="btn-000">Đăng Dự Án</a>
		</div>
	</div>
	<div class="wrap-list-duan">
		<ul class="clearfix">
			<li>
				<div class="img-intro pull-left">
					<div class="bgcover" style="background-image:url(<?= Yii::$app->view->theme->baseUrl . '/resources/images/img-duan-demo.jpg' ?>);"><a href="<?=Url::to(['/dashboard/statistics'])?>"></a></div>
					<a href="<?=Url::to(['/dashboard/statistics'])?>"><em class="icon-bar-chart"></em>View Stats</a>
					<a class="active-pro" href="#"><em class="fa fa-check"></em>Active Project</a>
				</div>
				<div class="intro-detail">
					<a href="#" class="icon-edit icon"></a>
					<div class="name-duan">
						<p class="name"><a href="<?=Url::to(['/dashboard/statistics'])?>">Lancaster X</a></p>
						<p class="date-post">Ngày đăng tin: 12/02/2016</p>
						<p class="loca-duan"><em class="icon-pointer"></em> Quận 4, Ho Chi Minh</p>
					</div>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud...</p>
				</div>
			</li>
			<li>
				<div class="img-intro pull-left">
					<div class="bgcover" style="background-image:url(<?= Yii::$app->view->theme->baseUrl . '/resources/images/img-duan-demo.jpg' ?>);"><a href="<?=Url::to(['/dashboard/statistics'])?>"></a></div>
					<a href="<?=Url::to(['/dashboard/statistics'])?>"><em class="icon-bar-chart"></em>View Stats</a>
					<a class="unactive-pro" href="#"><em class="fa fa-close"></em> Inactive Project</a>
				</div>
				<div class="intro-detail">
					<a href="#" class="icon-edit icon"></a>
					<div class="name-duan">
						<p class="name"><a href="<?=Url::to(['/dashboard/statistics'])?>">Lancaster X</a></p>
						<p class="date-post">Ngày đăng tin: 12/02/2016</p>
						<p class="loca-duan"><em class="icon-pointer"></em> Quận 4, Ho Chi Minh</p>
					</div>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud...</p>
				</div>
			</li>
			<li>
				<div class="img-intro pull-left">
					<div class="bgcover" style="background-image:url(<?= Yii::$app->view->theme->baseUrl . '/resources/images/img-duan-demo.jpg' ?>);"><a href="<?=Url::to(['/dashboard/statistics'])?>"></a></div>
					<a href="<?=Url::to(['/dashboard/statistics'])?>"><em class="icon-bar-chart"></em>View Stats</a>
					<a class="active-pro" href="#"><em class="fa fa-check"></em>Active Project</a>
				</div>
				<div class="intro-detail">
					<a href="#" class="icon-edit icon"></a>
					<div class="name-duan">
						<p class="name"><a href="<?=Url::to(['/dashboard/statistics'])?>">Lancaster X</a></p>
						<p class="date-post">Ngày đăng tin: 12/02/2016</p>
						<p class="loca-duan"><em class="icon-pointer"></em> Quận 4, Ho Chi Minh</p>
					</div>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud...</p>
				</div>
			</li>
			<li>
				<div class="img-intro pull-left">
					<div class="bgcover" style="background-image:url(<?= Yii::$app->view->theme->baseUrl . '/resources/images/img-duan-demo.jpg' ?>);"><a href="<?=Url::to(['/dashboard/statistics'])?>"></a></div>
					<a href="<?=Url::to(['/dashboard/statistics'])?>"><em class="icon-bar-chart"></em>View Stats</a>
					<a class="unactive-pro" href="#"><em class="fa fa-close"></em> Inactive Project</a>
				</div>
				<div class="intro-detail">
					<a href="#" class="icon-edit icon"></a>
					<div class="name-duan">
						<p class="name"><a href="<?=Url::to(['/dashboard/statistics'])?>">Lancaster X</a></p>
						<p class="date-post">Ngày đăng tin: 12/02/2016</p>
						<p class="loca-duan"><em class="icon-pointer"></em> Quận 4, Ho Chi Minh</p>
					</div>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud...</p>
				</div>
			</li>
		</ul>
	</div>
</div>