<?php
namespace frontend\controllers;

use yii\rest\ActiveController;
use frontend\models\Elastic;
use yii\helpers\Url;
use vsoft\ad\models\AdProduct;
use vsoft\express\components\StringHelper;
use frontend\models\MapSearch;
use yii\web\Request;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\yii\db;

class MapController extends ActiveController {
	
	public $modelClass = 'frontend\models\MapSearch';
	
	public function actionSearchProject() {
		$v = \Yii::$app->request->get('v');
		
		$response = [];
		
		$result = Elastic::searchProjects($v);
		
		if($result['hits']['total'] == 0) {
			$result = Elastic::searchProjects(Elastic::transform($v));
		}

		foreach ($result['hits']['hits'] as $k => $hit) {
			$response[$k]['full_name'] = $hit['_source']['full_name'];
			$response[$k]['id'] = $hit['_id'];
		}
		
		return $response;
	}
	
	public function actionGet() {
		$mapSearch = new MapSearch();
		$mapSearch->load(\Yii::$app->request->get());
		
		$response = [];
		
		$result = $mapSearch->search();
		
		if($mapSearch->rl) {
			$list = $result['aggregations']['rl']['hits'];
				
			$response['rl'] = $this->renderPartial('@frontend/web/themes/mv_desktop1/views/ad/_partials/side-list', ['searchModel' => $mapSearch, 'list' => $list]);
		}
		
		if($mapSearch->ra) {
			$allowArea = [
				'city' => ['id'],
				'district' => ['id', 'city_id'],
				'ward' => ['id', 'district_id', 'city_id'],
				'street' => ['id']
			];
			
			if(in_array($mapSearch->ra, array_keys($allowArea))) {
				$allowKey = $allowArea[$mapSearch->ra];
				$key = $mapSearch->ra_k;
				
				if(in_array($key, $allowKey)) {
					$areas = ArrayHelper::map($result['aggregations']['ra']['buckets'], "key", "doc_count");
						
					if(isset($areas[0])) {
						unset($areas[0]);
					}
						
					$table = 'ad_' . $mapSearch->ra;
					$value = ($key == 'id') ? $mapSearch->getAttribute($mapSearch->ra . '_id') : $mapSearch->getAttribute($key);
					
					$areasDb = (new Query())->select("id, center, geometry, name, pre")->from($table)->where([$key => $value])->all();
						
					foreach ($areasDb as &$area) {
						if(isset($areas[$area['id']])) {
							$area['total'] = $areas[$area['id']];
						} else {
							$area['total'] = 0;
						}
					}
						
					$response['ra'] = $areasDb;
				}
			}
		}
		
		if($mapSearch->rm) {
			$products = [];
			
			foreach ($result['aggregations']['rm']['hits']['hits'] as $hit) {
				$products[] = array_values($hit['_source']);
			}
			
			$response['rm'] = $products;
		}
		return $response;
	}
	
	public function getType() {
		$pathInfo = parse_url(\Yii::$app->request->referrer);
		$path = str_replace('/', '', $pathInfo['path']);
		
		return ($path == \Yii::t('url', 'nha-dat-ban')) ? AdProduct::TYPE_FOR_SELL : AdProduct::TYPE_FOR_RENT;
	}
    
    public function getArea($area, $where) {
    	$query = new Query();
    
    	$select = ['id', 'center', 'name', 'geometry'];
    
    	if(($area != 'city')) {
    		$select[] = 'pre';
    	}
    
    	$areas = $query->from('ad_' . $area)->select($select)->where($where)->all();
    
    	return $areas;
    }
	
	public function actionSearch() {
		$v = \Yii::$app->request->get('v');
		$vTransform = Elastic::transform($v);
		
		if(\Yii::$app->request->isAjax) {
			$response = [];
			
			if(StringHelper::startsWith($vTransform, 'mv')) {
				$id = str_replace('mv', '', $vTransform);
				$product = AdProduct::findOne($id);
				
				if($product) {
					$response['address'] = $product->address;
					$response['url'] = $product->urlDetail();
				}
			} else {
				$result = Elastic::searchAreasRankByTotal($v);
				
				if($result['hits']['total'] == 0) {
					$result = Elastic::searchAreasRankByTotal($vTransform);
				}
				
				foreach ($result['hits']['hits'] as $k => $hit) {
	    			$response[$k] = $hit['_source'];
	    			$response[$k]['url_sale'] = Url::to(['/ad/index1', 'params' => $hit['_source']['slug']]);
	    			$response[$k]['url_rent'] = Url::to(['/ad/index2', 'params' => $hit['_source']['slug']]);
	    			$response[$k]['type'] = $hit['_type'];
	    			$response[$k]['id'] = $hit['_id'];
	    		}
				 
				if(!$response && is_numeric($v)) {
					$product = AdProduct::findOne($v);
					 
					if($product) {
						$response['address'] = $product->address;
						$response['url'] = $product->urlDetail();
					}
				}
			}
			
			return $response;
		} else {
			$id = str_replace('mv', '', $vTransform);
			$product = AdProduct::findOne($id);
			
			if($product) {
				return $this->redirect($product->urlDetail());
			} else {
				return $this->redirect(Url::to(['/ad/index1']));
			}
		}
	}
}