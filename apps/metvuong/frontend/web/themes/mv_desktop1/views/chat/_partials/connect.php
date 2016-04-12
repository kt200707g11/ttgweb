<?php
use yii\web\View;
use yii\helpers\Url;
use frontend\models\Chat;
if(!Yii::$app->user->isGuest) {
    $debug = !empty(Yii::$app->params['chat']['debug']) ? ( 'true') :  ( 'false');
    $script = "var xmpp_jid = '" . Yii::$app->user->identity->username . "';" .
        "var xmpp_dm = '" . Chat::find()->getDomain() . "';" .
        "var xmpp_key = '" . Chat::find()->getKey() . "';" .
        "var xmpp_debug = ".$debug.";";

    Yii::$app->getView()->registerJs($script, View::POS_HEAD);

    Yii::$app->getView()->registerJsFile('/js/libs/handlebars/handlebars.min.js', ['position' => View::POS_BEGIN]);

    Yii::$app->getView()->registerJsFile('/js/strophe.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/strophe.chatstates.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/strophe.disco.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/strophe.muc.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/strophe.ping.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/strophe.pubsub.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/strophe.register.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/strophe.roster.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/chat.ui.js', ['position' => View::POS_BEGIN]);
    Yii::$app->getView()->registerJsFile('/js/lib/chat.js', ['position' => View::POS_BEGIN]);

    $parseUrl = Yii::$app->urlManager->parseRequest(Yii::$app->request);
    $urlBase = !empty($parseUrl[0]) ? $parseUrl[0] : '';
    ?>
    <script>
        $(document).ready(function () {
            chatUI.connect();
            $(document).bind('chat/receiveMessage', function (event, msg, params) {
                chatUI.setConservation(params.from, params.to);
                if(params.type == 'chat'){
                    if(params.chatType != chatUI.MSG_SEND_ME){
                        chatUI.notify(chatUI.NOTIFY_CHAT, 1);
                    }
                    chatUI.loadMessageToBox(msg, params);
                }else if(params.type == 'notify'){
                    chatUI.notify(chatUI.NOTIFY_OTHER, params.total);
                    var timer = 0;
                    timer = setTimeout(function () {
                        $.ajax({
                            type: "post",
                            url: "<?=Url::to(['/notification/update', 'username'=> Yii::$app->user->identity->username])?>",
                            data: {id: true},
                            success: function (data) {

                            }
                        });
                    }, 500);
                }else if(params.type == 'online'){
                    console.log('------------------', params);
                }
//                if(!checkMobile()){
//                    $('.wrapNotifyTotal').find('#notifyTotal').hide();
//                }

            });

            $(document).bind('chat/afterConnect', function (event, data) {
                $(document).trigger('chat/addWrapBoxChat');
            });

            $(document).bind('chat/addBoxChat', function (event, user) {
                var template = Handlebars.compile($("#item-box-chat-template").html());
                var html = template({});
                html = $(html).attr('chat-to', user);
                $('.wrap-items-chat').append(html);
            });

            $(document).bind('chat/addWrapBoxChat', function (event, user) {
                var template = Handlebars.compile($("#wrap-items-chat-template").html());
                var html = template({});
                $('body').append(html);
            });

            $(document).on('click', '.chat-now', function (e) {
                $('.box-chat-footer').loading();
                user = $(this).attr('data-chat-user');
                console.log(user);
                if (user) {
                    chatBoxExist = chatUI.getBoxChat('.item-box-chat', '<?=Yii::$app->user->identity->username?>', user);
                    if(!chatBoxExist){
                        $(document).trigger('chat/addBoxChat', [user]);
                        chatBoxExist = chatUI.getBoxChat('.item-box-chat', '<?=Yii::$app->user->identity->username?>', user);
                        $.ajax({
                            type: "get",
                            dataType: 'html',
                            url: '/chat/with/' + user,
                            success: function (data) {
                                chatBoxExist.find('.box-chat-footer').append(data);
                                Chat.historyMessage(user + '@<?=Chat::DOMAIN?>');
                            }
                        });
                    }
                    if(chatBoxExist) {
                        $('.wrap-items-chat').removeClass('hide');
                        $('body').loading({done:true});
                    }

                }
                return false;
            });

            $(document).bind('chat/readNotify', function (event, type) {
                if(type == chatUI.NOTIFY_CHAT){
                    $('#notifyChat').remove();
                    $('#notifyTotal').remove();
                }
            });
        });
    </script>
    <script id="wrap-items-chat-template" type="text/x-handlebars-template">
        <div class="wrap-items-chat hide">
            <div class="more-box-chat hide">
                <div class="dropdown">
                    <a href="#" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="icon-mv"><span class="icon-bubbles-icon"></span></span>5
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dLabel">
                        <li>a</li>
                        <li>a</li>
                        <li>a</li>
                        <li>a</li>
                    </ul>
                </div>
            </div>
        </div>
    </script>

    <script id="item-box-chat-template" type="text/x-handlebars-template">
        <div class="item-box-chat" chat-from="<?=Yii::$app->user->identity->username?>" chat-to="">
            <div class="box-chat-footer">
                <a href="#" class="close-box">
                    <span class="icon-mv"><span class="icon-close-icon"></span></span>
                </a>

            </div>
        </div>
    </script>
    <?php
}
?>