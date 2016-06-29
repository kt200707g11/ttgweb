<?php 
use yii\helpers\Url;
use vsoft\ad\models\AdProduct;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

$categoriesDb = \vsoft\ad\models\AdCategory::getDb();
$categories = $categoriesDb->cache(function($categoriesDb){
    return \vsoft\ad\models\AdCategory::find()->indexBy('id')->asArray(true)->all();
});
$types = AdProduct::getAdTypes();

foreach ($products as $product):
	echo $this->render('/ad/_partials/list-item', ['product' => $product, 'categories'=>$categories, 'types'=>$types]);
endforeach;
?>