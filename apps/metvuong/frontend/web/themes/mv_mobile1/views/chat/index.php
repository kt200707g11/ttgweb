<?php
use yii\helpers\Url;
use frontend\models\Chat;

$params = [':jid' => Chat::find()->getJid(Yii::$app->user->identity->username)];
$jid_id = Yii::$app->dbChat->createCommand('SELECT jid_id FROM tig_ma_jids tmj WHERE jid=:jid')->bindValues($params)->queryOne();
if(!empty($jid_id)){
	$sql = 'SELECT tbl.* '.
		'FROM (SELECT owner_id, buddy_id, ts, body, direction, IF(owner_id = :jid_id, buddy_id, owner_id) AS withuser FROM tig_ma_msgs tmm WHERE :jid_id IN (owner_id, buddy_id) AND (owner_id != buddy_id) ORDER BY ts DESC) as tbl '.
		'GROUP BY tbl.withuser ORDER BY tbl.ts DESC';
	$msgs = Yii::$app->dbChat->createCommand($sql)->bindValues([':jid_id'=>$jid_id['jid_id']])->queryAll();
}
?>
<div class="chat-history">
	<div class="title-top">Chat history</div>
	<div class="chat-list clearfix">
		<?php
		if(!empty($msgs)) {
			foreach($msgs as $msg){
				$jid_user = Yii::$app->get('dbChat')->cache(function ($db) use ($msg) {
					return Yii::$app->get('dbChat')->createCommand('SELECT jid FROM tig_ma_jids tmj WHERE jid_id=:jid_id')->bindValues([':jid_id'=>$msg['withuser']])->queryOne();
				});
				if(!empty($jid_user['jid'])){
					$username = Chat::find()->getUsername($jid_user['jid']);
					$user = \frontend\models\User::find()->where(['username' => $username])->one();
				}
				if(!empty($user->profile)){
			?>
					<div class="item" chat-to="<?=$user->username;?>">
						<a href="<?= Url::to(['/chat/with', 'username' => $user->username]) ?>">
							<span class="wrap-img"><img src="<?=$user->profile->getAvatarUrl();?>" alt=""></span>
							<div class="chat-detail">
								<span class="pull-right time-chat"><?=$msg['ts'];?></span>
								<span class="name"><?=$user->profile->getDisplayName();?></span>
								<span><?=$msg['body'];?></span>
							</div>
						</a>
					</div>
			<?php
				}
			}
		}
		?>
	</div>
</div>

<script id="chat-receive-template" type="text/x-handlebars-template">
	<div class="item" chat-to="{{to}}">
		<a href="{{chatUrl}}">
			<span class="wrap-img"><img src="{{avatarUrl}}" alt=""></span>
			<div class="chat-detail">
				<span class="pull-right time-chat">{{time}}</span>
				<span class="name">{{fromName}}</span>
				<span>{{msg}}</span>
			</div>
		</a>
	</div>
</script>
