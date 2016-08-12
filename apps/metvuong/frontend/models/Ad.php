<?php
/**
 * Created by PhpStorm.
 * User: vinhnguyen
 * Date: 12/8/2015
 * Time: 10:14 AM
 */

namespace frontend\models;
use vsoft\ad\models\AdImages;
use vsoft\ad\models\AdProduct;
use vsoft\ad\models\AdProductRating;
use vsoft\ad\models\AdProductSaved;
use frontend\models\UserActivity;
use vsoft\tracking\models\base\ChartStats;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use vsoft\news\models\CmsShow;
use vsoft\ad\models\AdBuildingProject;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class Ad extends Component
{
    /**
     * @return mixed
     */
    public static function find()
    {
        return Yii::createObject(Ad::className());
    }

    /**
     * @return string
     */
    public function redirect(){
        $url = Url::home();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $searchParams = $post;
            unset($searchParams['_csrf']);
            unset($searchParams['valSearch']);
            unset($searchParams['activeSearch']);
            $searchParams = array_filter($searchParams, 'strlen');
            if(!empty($post['activeSearch'])){
                switch($post['activeSearch']){
                    case 1:
                        $url = Url::to(ArrayHelper::merge(['/ad/index'], $searchParams));
                        break;
                    case 2:
                        $url = Url::to(ArrayHelper::merge(['/ad/post'], $searchParams));
                        break;
                    case 3:
                        if(!empty($post['newsType']) && $post['newsType'] == 1){
                            if($arrCats = array_values(Yii::$app->params["news"]["widget-category"])){
//                                $detail = CmsShow::find()->where('catalog_id IN ('.implode($arrCats, ',').')')->orderBy('id DESC')->one();
                                $cat_id = empty($post["newsCat"]) == false ? $post["newsCat"] : 0; //implode($arrCats, ',');
                                $detail = CmsShow::find()->where('catalog_id IN ('.$cat_id.')')->orderBy('id DESC')->one();
                                if(!empty($detail))
                                    $url = Url::to(['news/view', 'id' => $detail->id, 'slug' => $detail->slug, 'cat_id' => $detail->catalog->id, 'cat_slug' => $detail->catalog->slug]);
                                else
                                    $url = Url::to(['news/findnotfound']);
                            }
                        }else if(!empty($post['newsType']) && $post['newsType'] == 2){
                            $bp_id = empty($post["project"]) == false ? $post["project"] : 0;
                            $model = AdBuildingProject::find()->andWhere('id = :id',[':id' => $bp_id])->one();
                            if(!empty($model)) {
                                $url = Url::to(['/building-project/view', 'slug' => $model->slug]);
                            }
                            else {
                                $url = Url::to(['news/findnotfound']);
                            }
                        }
                        break;
                }
            }
            $cookie = new Cookie([
                'name' => 'searchParams',
                'value' => json_encode($post),
                'expire' => time() + 60 * 60 * 24 * 30, // 30 days
//            'domain' => '.lancaster.vn' // <<<=== HERE
            ]);
            Yii::$app->getResponse()->getCookies()->add($cookie);
        }
        Yii::$app->getResponse()->redirect($url);
        return $url;
    }

    private function checkLogin(){
        if(Yii::$app->user->isGuest){
            throw new NotFoundHttpException('You must login !');
        }
        return true;
    }

    public function favorite(){
        $this->checkLogin();
        if(Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = Yii::$app->request->post();
            if(!empty($post['id'])){
                $time = time();
                if(($adSaved = AdProductSaved::findOne(['product_id'=>$post['id'], 'user_id'=>Yii::$app->user->id])) === null){
                    $adSaved = new AdProductSaved();
                    $adSaved->product_id = $post['id'];
                    $adSaved->user_id = Yii::$app->user->id;
                    $adSaved->saved_at = $time;
                }else{
//                    $saved_at = $adSaved->saved_at;
                    $adSaved->saved_at = !empty($post['stt']) ? time() : 0;
                }
                $adSaved->validate();
                if(!$adSaved->hasErrors()){
                    if(Yii::$app->user->id != $adSaved->product->user_id) {
                        $adSaved->save();
                        UserActivity::me()->saveActivity(UserActivity::ACTION_AD_FAVORITE, [
                            'owner' => Yii::$app->user->id,
                            'product' => $adSaved->product_id,
                            'buddy' => $adSaved->product->user_id,
                            'saved_at' => $adSaved->saved_at,
                        ], $adSaved->product_id);

//                         //save chart_stats favorite
//                        if($adSaved->saved_at > 0)
//                            Tracking::find()->saveChartStats($adSaved->product_id, date("d-m-Y", $time), 'favorite', 1);
//                        else {
//                            // kiem tra product duoc favorite ngay nao va -1 favorite
//                            $chart_stats_pid = ChartStats::find()->where(['product_id' => $adSaved->product_id, 'date' => date(Chart::DATE_FORMAT, $saved_at)])
//                                ->andWhere(['>', 'favorite', 0])->orderBy(['created_at' => SORT_DESC])->one();
//                            if(is_object($chart_stats_pid)) {
//                                if (count($chart_stats_pid) > 0) {
//                                    $chart_stats_pid->favorite = $chart_stats_pid->favorite - 1;
//                                    $chart_stats_pid->save();
//                                }
//                            } else {
//                                Tracking::syncFavorite($adSaved->product_id);
//                            }
////                            Tracking::find()->saveChartStats($adSaved->product_id, date("d-m-Y", $time), 'favorite', 0);
//                        }
                    }
                }
                return ['statusCode'=>200, 'parameters'=>['msg'=>Yii::$app->session->getFlash('notify_other')]];
            }
        }
        return ['statusCode'=>404, 'parameters'=>['msg'=>'']];
    }

    public function report(){
        $this->checkLogin();
        if(Yii::$app->request->isPost && Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = Yii::$app->request->post();
            if(!empty($post['user_id'])){
                return ['statusCode'=>200, 'parameters'=>['msg'=>'']];
            }
        }
        return ['statusCode'=>404, 'parameters'=>['msg'=>'user is not found']];
    }

    public function rating(){
        $this->checkLogin();
        if(Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = Yii::$app->request->post();
            if(($adProduct = AdProduct::findOne(['id'=>$post['id']])) !== null && !empty($post['core'])){
                if(($adProductRating = AdProductRating::findOne(['user_id'=>Yii::$app->user->id, 'product_id'=>$adProduct->id])) === null){
                    $adProductRating = Yii::createObject(['class' => AdProductRating::className(),
                        'user_id'=>Yii::$app->user->id,
                        'product_id'=>$adProduct->id,
                        'core'=>$post['core'],
                        'rating_at'=>time(),
                    ]);
                    $adProductRating->validate();
                    if(!$adProductRating->hasErrors()){
                        $adProductRating->save();
                        $_rating = $adProductRating->core;
                        $core = AdProductRating::findBySql('SELECT AVG(core) as avgCore FROM '.AdProductRating::tableName().' WHERE product_id = '.$adProduct->id)->one();
                        if(!empty($core->avgCore)){
                            $_rating = $core->avgCore;
                        }
                        $adProduct->updateAttributes(['rating'=>$_rating]);
                    }
                    return ['statusCode'=>200, 'parameters'=>['msg'=>'Rating successs', 'data'=>round($_rating)]];
                };
                return ['statusCode'=>404, 'parameters'=>['msg'=>'You rated']];
            }
            return ['statusCode'=>404, 'parameters'=>['msg'=>'Missing data']];
        }
    }

    public function homePageRandom(){
        $sql = "SELECT DISTINCT id FROM ad_product INNER JOIN `ad_product_addition_info` ON ad_product_addition_info.product_id = ad_product.id ".
            " WHERE status=1 AND verified=1 AND is_expired=0".
            " ORDER BY score DESC limit 6;";
        $ids = AdProductSearch::getDb()->createCommand($sql)->queryColumn();
        $ids = !empty($ids) ? implode(',', $ids) : [];
        $query = AdProductSearch::find();
        $query->select('ad_product.id, ad_product.updated_at, ad_product.show_home_no, ad_product.home_no, ad_product.city_id, ad_product.district_id, ad_product.ward_id, ad_product.street_id, ad_product.lat, ad_product.lng,
			ad_product.price, ad_product.area, ad_product_addition_info.room_no, ad_product_addition_info.toilet_no, ad_product.created_at, ad_product.category_id, ad_product.type, ad_images.file_name,
			 ad_images.folder');
        $query->innerJoin('ad_product_addition_info', 'ad_product_addition_info.product_id = ad_product.id');
//        $query->where(['status' => 1, 'verified' => 1, 'is_expired' => 0]);
        if(!empty($ids)){
            $query->andWhere('ad_product.id IN ('.$ids.')');
        }
        $query->andWhere('ad_product.status=1');
        $query->leftJoin('ad_images', 'ad_images.order = 0 AND ad_images.product_id = ad_product.id');
        $query->groupBy('ad_product.id');
        $products = $query->orderBy(['ad_product.score'=>SORT_DESC])->limit(6)->all();
        return $products;
    }

    public function listingFavorite(){
        $query = AdProductSearch::find();
        $query->select('ad_product.id, ad_product.updated_at, ad_product.show_home_no, ad_product.home_no, ad_product.city_id, ad_product.district_id, ad_product.ward_id, ad_product.street_id, ad_product.lat, ad_product.lng,
			ad_product.price, ad_product.area, ad_product_addition_info.room_no, ad_product_addition_info.toilet_no, ad_product.created_at, ad_product.category_id, ad_product.type, ad_images.file_name,
			 ad_images.folder');
        $query->innerJoin('ad_product_addition_info', 'ad_product_addition_info.product_id = ad_product.id');
        $query->innerJoin('ad_product_saved', 'ad_product_saved.product_id = ad_product.id');
//        $query->where(['status' => 1, 'verified' => 1, 'is_expired' => 0]);
        $query->andWhere('ad_product_saved.user_id IN ('.Yii::$app->user->id.')');
        $query->andWhere('ad_product_saved.saved_at != 0');
        $query->andWhere('ad_product.status=1');
        $query->leftJoin('ad_images', 'ad_images.order = 0 AND ad_images.product_id = ad_product.id');
        $query->groupBy('ad_product.id');
        $products = $query->orderBy(['ad_product.score'=>SORT_DESC])->limit(6)->all();
        return $products;
    }

    public function listingOfBuilding($building_id, $type){
        $query = AdProductSearch::find();
        $query->select('ad_product.id, ad_product.updated_at, ad_product.show_home_no, ad_product.home_no, ad_product.city_id, ad_product.district_id, ad_product.ward_id, ad_product.street_id, ad_product.lat, ad_product.lng,
			ad_product.price, ad_product.area, ad_product_addition_info.room_no, ad_product_addition_info.toilet_no, ad_product.created_at, ad_product.category_id, ad_product.type, ad_images.file_name,
			 ad_images.folder');
        $query->innerJoin('ad_product_addition_info', 'ad_product_addition_info.product_id = ad_product.id');
        $query->andWhere('ad_product.type = '.$type.' AND ad_product.project_building_id = '.$building_id);
        $query->andWhere('ad_product.status=1');
        $query->leftJoin('ad_images', 'ad_images.order = 0 AND ad_images.product_id = ad_product.id');
        $query->groupBy('ad_product.id');
        $products = $query->orderBy(['ad_product.updated_at'=>SORT_DESC])->limit(4)->all();
        return $products;
    }
}