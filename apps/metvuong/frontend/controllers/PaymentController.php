<?php

namespace frontend\controllers;
use frontend\components\Controller;
use frontend\models\Chat;
use frontend\models\Profile;
use Yii;
use yii\base\Event;
use yii\base\ViewEvent;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\View;
use frontend\models\Payment;

class PaymentController extends Controller
{
    public $layout = '@app/views/layouts/layout';

    public function beforeAction($action)
    {
        $this->view->params['noFooter'] = true;
        return parent::beforeAction($action);
    }

    public function actionIndex(){
        if(Yii::$app->request->isPost) {
            Payment::me()->payByBank();
            Payment::me()->payByMobiCard();
        }
        return $this->render('index');
    }

    public function actionPackage(){
        $this->view->title = Yii::t('meta', 'services');
        $this->view->params['menuPricing'] = true;
        return $this->render('package/index');
    }

    public function actionReturn(){

    }

    public function actionCancel(){

    }

}
