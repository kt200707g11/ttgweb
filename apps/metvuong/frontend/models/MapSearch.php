<?php
namespace frontend\models;

use yii\db\Query;
use vsoft\ad\models\AdProduct;
use yii\data\Pagination;
use vsoft\express\components\StringHelper;

class MapSearch extends AdProduct {
	
	public $price_min;
	public $price_max;
	
	public $size_min;
	public $size_max;
	
	public $created_before;
	public $order_by;
	
	public $room_no;
	public $toilet_no;
	
	public $rect;
	public $rm;
	public $ra;
	public $ra_k;
	public $rl;
	
	function rules() {
		return array_merge(parent::rules(), [
			[['order_by', 'rect', 'ra', 'ra_k'], 'string'],
			[['price_min', 'price_max', 'size_min', 'size_max', 'created_before', 'room_no', 'toilet_no', 'rm', 'rl'], 'number']
		]);
	}
	
	function formName() {
		return '';
	}
	
	public function search($args) {
		$this->load($args);

		$query = new Query();

		$query->select('ad_product.id, ad_product.area, ad_product.price, ad_product.lng, ad_product.lat, ad_product_addition_info.room_no, ad_product_addition_info.toilet_no');
		$query->from('ad_product');
		$query->innerJoin('ad_product_addition_info', 'ad_product_addition_info.product_id = ad_product.id');
		
		$where = ['ad_product.status' => 1, 'ad_product.is_expired' => 0, 'ad_product.verified' => 1];
		$totalInitWhere = count($where);
		
		if($this->street_id) {
			$where['ad_product.street_id'] = intval($this->street_id);
		}
		
		if($this->ward_id) {
			$where['ad_product.ward_id'] = intval($this->ward_id);
		}
		
		if($this->project_building_id) {
			$where['ad_product.project_building_id'] = intval($this->project_building_id);
		}
		
		if(count($where) == $totalInitWhere && $this->district_id) {
			$where['ad_product.district_id'] = intval($this->district_id);
		}
		
		if(count($where) == $totalInitWhere) {
			if($this->city_id) {
				$where['ad_product.city_id'] = intval($this->city_id);
			} else {
				$where['ad_product.city_id'] = AdProduct::DEFAULT_CITY;
			}
		}
		
		if($this->type) {
			$where['ad_product.type'] = intval($this->type);
		} else {
			$where['ad_product.type'] = AdProduct::TYPE_FOR_SELL;
		}
		
		if($this->category_id) {
			$where['ad_product.category_id'] = explode(',', $this->category_id);
		}
		
		if($this->owner) {
			$where['ad_product.owner'] = intval($this->owner);
		}
		
		$query->where($where);
		
		if($this->price_min) {
			$query->andWhere(['>=', 'ad_product.price', intval($this->price_min)]);
		}
			
		if($this->price_max) {
			$query->andWhere(['<=', 'ad_product.price', intval($this->price_max)]);
		}
		
		if($this->size_min) {
			$query->andWhere(['>=', 'ad_product.area', intval($this->size_min)]);
		}
		
		if($this->size_max) {
			$query->andWhere(['<=', 'ad_product.area', intval($this->size_max)]);
		}
		
		if($this->created_before) {
			$query->andWhere(['>=', 'ad_product.created_at', strtotime($this->created_before)]);
		}
		
		if($this->room_no) {
			$query->andWhere(['>=', 'ad_product_addition_info.room_no', intval($this->room_no)]);
		}
		
		if($this->toilet_no) {
			$query->andWhere(['>=', 'ad_product_addition_info.toilet_no', intval($this->toilet_no)]);
		}
		
		if($this->rect) {
			$rect = explode(',', $this->rect);
			
			$query->andWhere(['>=', 'ad_product.lat', $rect[0]]);
			$query->andWhere(['<=', 'ad_product.lat', $rect[2]]);
			$query->andWhere(['>=', 'ad_product.lng', $rect[1]]);
			$query->andWhere(['<=', 'ad_product.lng', $rect[3]]);
		}
		
		return $query;
	}
	
	public function getList($query) {
		$listQuery = clone $query;
			
		$countQuery = clone $listQuery;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->setPageSize(\Yii::$app->params['listingLimit']);
				
		$listQuery->offset($pages->offset);
		$listQuery->limit($pages->limit);
		
		$sort = $this->order_by ? $this->order_by : '-score';
		$doa = StringHelper::startsWith($sort, '-') ? 'DESC' : 'ASC';
		$sort = str_replace('-', '', $sort);
				
		$listQuery->orderBy("$sort $doa");
		
		$listQuery->addSelect([
			"ad_product.updated_at",
			"ad_product.show_home_no",
			"ad_product.home_no",
			"ad_product.street_id",
			"ad_product.ward_id",
			"ad_product.district_id",
			"ad_product.category_id",
			"ad_product.type",
		]);
		
		return ['products' => $listQuery->all(), 'pages' => $pages];
	}
	
	function fetchValues() {
		if(!$this->district_id) {
			if($this->street_id) {
				$this->district_id = $this->street->district->id;
			} else if($this->ward_id) {
				$this->district_id = $this->ward->district->id;
			} else if($this->project_building_id) {
				if($this->projectBuilding->district) {
					$this->district_id = $this->projectBuilding->district->id;
				}
			}
		}
	
		if(!$this->city_id) {
			if($this->district_id) {
				$this->city_id = $this->district->city->id;
			} else {
				$this->city_id = self::DEFAULT_CITY;
			}
		}
	
		if(!$this->type) {
			$this->type = AdProduct::TYPE_FOR_SELL;
		}
	}
}