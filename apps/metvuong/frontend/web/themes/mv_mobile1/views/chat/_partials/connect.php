<?php
use yii\web\View;
use yii\helpers\Url;
use frontend\models\Chat;
if(!Yii::$app->user->isGuest) {
    $script = "var xmpp_jid = '" . Yii::$app->user->identity->username . "';var xmpp_dm = '" . Chat::find()->getDomain() . "';var xmpp_key = '" . Chat::find()->getKey() . "';";
    Yii::$app->getView()->registerJs($script, View::POS_HEAD);

    Yii::$app->getView()->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js', ['position' => View::POS_BEGIN]);

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
                        chatUI.notify(chatUI.NOTIFY_CHAT, 2);
                    }
                    if ('<?=$urlBase?>' == 'chat/with') {
                        chatUI.loadMessageToBox(msg, params);
                    } else if ('<?=$urlBase?>' == 'chat/index') {
                        chatUI.loadMessageToList(msg, params);
                    }
                }else if(params.type == 'notify'){
                    chatUI.notify(chatUI.NOTIFY_OTHER, params.total);
                }
            });
            $(document).bind('chat/readNotify', function (event, type) {
                if(type == chatUI.NOTIFY_CHAT){
                    $('#notifyChat').remove();
                    $('#notifyTotal').remove();
                }
            });
        });
    </script>
    <?php
}
?>