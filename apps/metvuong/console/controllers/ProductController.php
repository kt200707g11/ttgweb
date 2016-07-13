<?php
namespace console\controllers;

use console\models\Metvuong;
use vsoft\ad\models\AdImages;
use yii\console\Controller;
use vsoft\ad\models\AdProduct;
use vsoft\ad\models\AdBuildingProject;
use vsoft\ad\models\AdCity;
use frontend\models\Elastic;
use common\models\common\models;
use vsoft\ad\models\AdDistrict;
use yii\db\Query;

class ProductController extends Controller {
	
	public function actionCheckLatLng() {
		$projects = AdBuildingProject::find()->asArray(true)->all();
		$command = \Yii::$app->db->createCommand();
		
		foreach ($projects as $project) {
			$command->update(AdProduct::tableName(), ['lat' => $project['lat'], 'lng' => $project['lng']], 'project_building_id = ' . $project['id'])->execute();
		}
	}
	
	public function actionCheckExpired() {
		$now = time();
		$products = AdProduct::find()->where("`end_date` < {$now} AND `is_expired` = 0")->limit(1000)->asArray(true)->all();
		
		$connection = \Yii::$app->db;
		
		foreach ($products as $product) {
			$connection->createCommand()->update('ad_product', ['is_expired' => 1], 'id = ' . $product['id'])->execute();
			
			$totalType = ($product['type'] == AdProduct::TYPE_FOR_SELL) ? AdProduct::TYPE_FOR_SELL_TOTAL : AdProduct::TYPE_FOR_RENT_TOTAL;
			
			AdProduct::updateElasticCounter('city', $product['city_id'], $totalType, false);
			AdProduct::updateElasticCounter('district', $product['district_id'], $totalType, false);
			
			if($product['ward_id']) {
				AdProduct::updateElasticCounter('ward', $product['ward_id'], $totalType, false);
			}
			if($product['street_id']) {
				AdProduct::updateElasticCounter('street', $product['street_id'], $totalType, false);
			}
			if($product['project_building_id']) {
				AdProduct::updateElasticCounter('project_building', $product['project_building_id'], $totalType, false);
			}
		}
		
		echo 'Update Total: ' . count($products);
	}

    public function actionCheckScore(){
        $start = time();
        $products = AdProduct::find()->where("`score` = 0")->orderBy(['updated_at' => SORT_DESC])->limit(1000)->all();
        if(count($products) > 0) {
            $no = 0;
            foreach ($products as $product) {
                $score = AdProduct::calcScore($product);
                \Yii::$app->db->createCommand()->update(AdProduct::tableName(), ['score' => $score], 'id = ' . $product['id'])->execute();
                if($no >0 && $no % 100 == 0) {
                    print_r(PHP_EOL);
                    print_r("Checked {$no} records...");
                    print_r(PHP_EOL);
                }
                $no++;
            }
            $stop = time();
            $time = $stop-$start;
            print_r(PHP_EOL);
            print_r("Checked {$no} records... DONE! - Time: {$time}s");
        } else {
//            print_r(PHP_EOL);
            print_r(" Products have checked score!");
        }
    }

    public $code;
    public function options()
    {
        return ['code'];
    }
    public function optionAliases()
    {
        return ['code' => 'code'];
    }

    /*
        Marketing contact send mail: php yii product/send-mail-contact -code=123456
    */
    public function actionSendMailContact(){
        Metvuong::sendMailContact($this->code);
    }

    public function actionDownloadImage()
    {
        $images = AdImages::find()->where('folder = :f', [':f' => ''])->orWhere(['folder' => null])->limit(1000)->all();
        if(count($images) > 0){
            $no = 0;
            foreach ($images as $image) {
                $result = Metvuong::DownloadImage($image->file_name, $image->uploaded_at);
                if(!empty($result)){
                    $image->file_name = $result[0];
                    $image->folder = $result[1];
                    $image->update(false);
                }
                if($no > 0 && $no % 100 == 0) {
                    print_r(PHP_EOL);
                    print_r("Updated {$no} images...");
                    print_r(PHP_EOL);
                }
                $no++;
                sleep(1);
            }
            print_r(PHP_EOL);
            print_r("Updated {$no} images...");
            print_r(PHP_EOL);
        }
    }

}